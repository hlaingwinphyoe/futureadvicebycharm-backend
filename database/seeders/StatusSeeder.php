<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'sex' => ['Male', 'Female', 'Other'],
            'age' => ['day', 'month', 'year'],
            'transaction' => ['In', 'Out'],
            'status' => ['Booked', 'Pending', 'Finished', 'Cancelled', 'Incomplete', 'Approved'],
            'order' => ['Ordered', 'Received'],
            // 'weekdays' => ['တနင်္ဂနွေ', 'တနင်္လာ', 'အင်္ဂါ', 'ဗုဒ္ဓဟူး', 'ကြာသပတေး', 'သောကြာ', 'စနေ'],
            'weekdays' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ];

        foreach ($names as $index => $name_ary) {
            foreach ($name_ary as $name) {
                $slug = Str::slug($name);
                $status = Status::create([
                    'slug' =>  $slug,
                    'name' => $name,
                    'type' => $index
                ]);
            }
        }

        $currency = Currency::create([
            'slug' => 'mmk',
            'name' => 'Ks'
        ]);

        $currency->exchangerates()->create([
            'rate' => 1,
            'mmk' => 1,
            'user_id' => 1,
        ]);

        $currency = Currency::create([
            'slug' => 'thb',
            'name' => '฿'
        ]);
    }
}
