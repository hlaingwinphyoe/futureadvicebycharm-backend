<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Status;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    private $appointmentSvc, $mediaSvc;

    public function __construct(AppointmentService $appointmentSvc, MediaService $mediaSvc)
    {
        $this->appointmentSvc = $appointmentSvc;
        $this->mediaSvc = $mediaSvc;
    }

    public function getBookings($id)
    {
        $appointments = Appointment::query()
            ->with(['appointment_packages'])
            ->where('user_id', $id)
            ->filterOn()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $appointments = AppointmentResource::collection($appointments);

        return $this->sendResponse($appointments, 200);
    }

    public function makeAppointment(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'dob' => 'required|date|date_format:Y-m-d|before:tomorrow',
            'gender' => 'required|numeric|exists:statuses,id',
            'birthday' => 'required|numeric|exists:statuses,id',
            'desc' => 'required|string',
            'social_link' => 'required|string',
            'packages' => 'required',
            'packages.*' => 'numeric|exists:packages,id',
            'appointment_date' => 'required|date|date_format:Y-m-d|after:today'
        ]);

        try {
            DB::beginTransaction();

            $appointment = $this->appointmentSvc->store($request->all());

            DB::commit();

            return $this->sendResponse($appointment, 'Success!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function getAppointment($appointment_no)
    {
        try {
            $appointment = Appointment::with(['appointment_packages', 'user'])
                ->where('appointment_no', $appointment_no)
                ->first();

            $appointment = new AppointmentResource($appointment);
            return $this->sendResponse($appointment, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function paymentStore(Request $request, $appointment_no)
    {
        $request->validate([
            'transaction_no' => 'required_without:transaction_img|string',
            'transaction_img' => 'required_without:transaction_no|image|mimes:jpeg,png,jpg,gif|max:5120',
            'paymentype' => 'required|numeric|exists:banks,id'
        ]);

        $appointment = Appointment::where('appointment_no', $appointment_no)->first();
        try {
            DB::beginTransaction();
            if ($appointment) {

                $pendingStatus = Status::isType('status')->where('slug', 'pending')->first();

                $appointment->update([
                    'transaction_no' => $request->transaction_no,
                    'paymentype_id' => $request->paymentype,
                    'status_id' => $pendingStatus->id
                ]);

                if ($request->hasFile('transaction_img')) {
                    // Delete the old image
                    if ($appointment->transaction_image !== null) {
                        Storage::disk('public')->delete($appointment->transaction_image);
                    }
                    $mediaFormdata = [
                        'media' => $request->file('transaction_img'),
                        'type' => "payment",
                    ];

                    $url = $this->mediaSvc->storeMedia($mediaFormdata);

                    $appointment->update([
                        'transaction_image' => $url
                    ]);
                }

                DB::commit();

                return $this->sendResponse($appointment, 'Success!');
            } else {
                DB::rollBack();
                return $this->sendError("Not Found", 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function getBookingsDays()
    {
        $fullyBookedDays = DB::table('appointments')
            ->select(DB::raw('DATE(appointment_date) as date'), DB::raw('COUNT(*) as count'))
            ->where('appointment_date', '>=', now()->startOfDay())
            ->groupBy(DB::raw('DATE(appointment_date)'))
            ->having('count', '>=', 10)
            ->get()
            ->pluck('date')
            ->toArray();

        return $this->sendResponse($fullyBookedDays, 'Success!');
    }
}
