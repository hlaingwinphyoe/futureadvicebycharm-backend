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

        $total_price = 0;
        $th_total_price = 0;
        foreach ($formData['packages'] as $packageId) {
            $package = Package::findOrFail($packageId);

            $price = $package->price;
            $th_price = $package->th_price;
            $balance = $package->final_price;
            $th_balance = $package->th_final_price;
            $discount_amt = $package->discount_percent;

            $appointment->appointment_packages()->create([
                'package_id' => $package->id,
                'price' => $price,
                'th_price' => $th_price,
                'balance' => $balance,
                'th_balance' => $th_balance,
                'currency_id' => $package->currency_id,
                'th_currency_id' => $package->th_currency_id,
                'status_id' => $unPaidStatus->id,
                'discount_amt' => $discount_amt,
            ]);

            $appointment->update([
                'refer_id' => $package->astrologer_id,
            ]);

            $total_price += $balance;
            $th_total_price += $th_balance;
        }
        $appointment->update([
            'total_price' => $total_price,
            'balance' => $total_price,
            'th_total_price' => $th_total_price,
            'th_balance' => $th_total_price,
        ]);

        return $appointment;
    }
}
