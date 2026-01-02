<?php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\SystemInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $info = SystemInfo::create([
            'name' => "Future Advice by Charm",
            'address' => "Chatuchak, Bangkok, Thailand"
        ]);

        $phone = Phone::create([
            'phone' => '+66660857579'
        ]);

        $phone->system_infos()->sync($info);
    }
}
