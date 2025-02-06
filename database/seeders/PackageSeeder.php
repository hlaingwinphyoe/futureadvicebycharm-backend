<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Ex Comeback / No Contact',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Right Person',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Crush or Situationship Reading',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'About Your Business',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Now & Future Business Reading',
                'price' => 20000,
                'th_price' => 200,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'For Education',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'One Month Love Reading',
                'price' => 7000,
                'th_price' => 70,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Secret of Your Person',
                'price' => 7000,
                'th_price' => 70,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Improve Yourself',
                'price' => 7000,
                'th_price' => 70,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Your Love Reading',
                'price' => 20000,
                'th_price' => 200,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'For Relationship',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Your Foreign Luck',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
            [
                'name' => 'Palm General Reading',
                'price' => 10000,
                'th_price' => 100,
                'astrologer_id' => User::astrologer()->get()->random()->id,
            ],
        ];

        $th_currency = Currency::where('slug', 'thb')->first();
        $mm_currency = Currency::where('slug', 'mmk')->first();

        foreach ($packages as $package) {
            Package::firstOrCreate([
                'slug' => Str::slug($package['name'])
            ], [
                'slug' => Str::slug($package['name']),
                'name' => $package['name'],
                'price' => $package['price'],
                'th_price' => $package['th_price'],
                'astrologer_id' => $package['astrologer_id'],
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id
            ]);
        }
    }
}
