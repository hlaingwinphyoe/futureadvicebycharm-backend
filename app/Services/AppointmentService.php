<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Package;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    public function store(array $formData = [])
    {
        $user = User::findOrFail(Auth::id());
        $user->update([
            'email' => $formData['email'],
            'dob' => $formData['dob'],
            'gender_id' => $formData['gender'],
            'weekday_id' => $formData['birthday'],
            'social_link' => $formData['social_link'],
        ]);

        $appointment_month = Carbon::now()->format('ym');

        $latest_appointment = Appointment::where('appointment_month', intval($appointment_month))->orderBy('appointment_no', 'desc')->first();

        $appointment_no = $latest_appointment ? $latest_appointment->appointment_no + 1 : intval($appointment_month . '00001');

        $pendingStatus = Status::isType('status')->where('name', 'pending')->first();
        $unPaidStatus = Status::isType('status')->where('name', 'Incomplete')->first();

        $appointment = Appointment::create([
            'appointment_no' => $appointment_no,
            'appointment_month' => $appointment_month,
            'desc' => $formData['desc'],
            'user_id' => $user->id,
            'status_id' => $pendingStatus->id,
            'appointment_date' => $formData['appointment_date'],
        ]);

        foreach ($formData['packages'] as $packageId) {
            $package = Package::findOrFail($packageId);

            $appointment->appointment_packages()->create([
                'package_id' => $package->id,
                'price' => $package->price,
                'th_price' => $package->th_price,
                'balance' => $package->price,
                'th_balance' => $package->th_price,
                'currency_id' => $package->currency_id,
                'th_currency_id' => $package->th_currency_id,
                'status_id' => $unPaidStatus->id,
            ]);

            $appointment->update([
                'refer_id' => $package->astrologer_id,
                'total_price' => $appointment->total_price + $package->price,
                'balance' => $appointment->total_price + $package->price,
                'th_total_price' => $appointment->th_total_price + $package->th_price,
                'th_balance' => $appointment->th_total_price + $package->th_price,
            ]);
        }

        return $appointment;
    }
}
