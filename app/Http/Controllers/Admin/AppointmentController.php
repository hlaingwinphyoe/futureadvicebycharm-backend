<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Status;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $appointments = Appointment::query()
            ->with(['user'])
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($appointment) => [
                'id' => $appointment->id,
                'appointment_no' => $appointment->appointment_no,
                'customer_name' => $appointment->user ? $appointment->user->name : '',
                'customer_age' => $appointment->user ? $appointment->user->dob : '',
                'is_paid' => $appointment->is_paid,
                'book_packages' => $appointment->appointment_packages,
                'desc' => $appointment->desc,
                'total_price' => $appointment->total_price,
                'status' => $appointment->status ? $appointment->status->name : '',
                'created_at' => $appointment->created_at->format('d M, Y'),
            ]);

        return Inertia::render('Admin/Appointment/Index', [
            'appointments' => $appointments
        ]);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['user.gender', 'user.weekday', 'appointment_packages.package'])
            ->findOrFail($id);
        return Inertia::render('Admin/Appointment/Show', [
            'appointment' => $appointment
        ]);
    }

    public function update($id, $type)
    {
        $appointment = Appointment::findOrFail($id);
        $incomplete = Status::isType('status')->where('slug', 'incomplete')->first();
        $approved = Status::isType('status')->where('slug', 'approved')->first();
        $finished = Status::isType('status')->where('slug', 'finished')->first();
        if ($type == 'incomplete') {
            $appointment->update([
                'status_id' => $incomplete->id,
            ]);
        } else if ($type == 'approved') {
            $appointment->update([
                'status_id' => $approved->id,
                'is_paid' => true
            ]);
        } else {
            $appointment->update([
                'status_id' => $finished->id,
            ]);
        }

        return redirect()->route('admin.appointments.index')->with('success', 'Success');
    }
}
