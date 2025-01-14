<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'acc_name' => "Shin Lwin Thant",
                'acc_no' => "09753472946",
                'type' => 'K Pay'
            ],
            [
                'acc_name' => "Shin Lwin Thant",
                'acc_no' => "09753472946",
                'type' => 'Wave Money'
            ],
            [
                'acc_name' => "MR. SHIN LWIN THANT",
                'acc_no' => "09753472946",
                'type' => 'Bank Transfer'
            ]
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate([
                'type' => $bank['type']
            ], [
                'acc_name' => $bank['acc_name'],
                'acc_no' => $bank['acc_no'],
                'type' => $bank['type'],
            ]);
        }
    }
}
