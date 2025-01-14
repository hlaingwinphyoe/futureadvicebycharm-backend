<?php

namespace Database\Seeders;

use App\Models\Zodiac;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ZodiacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $zodiacs = [
            [
                'name' => "Aries",
                'period' => "Mar 21 - Apr 19",
                // 'image' => asset('imgs/signs/aries.png')
            ],
            [
                'name' => "Taurus",
                'period' => "Apr 20 - May 20",
                // 'image' => asset('imgs/signs/tarius.png')
            ],
            [
                'name' => "Gemini",
                'period' => "May 21 - Jun 20",
                // 'image' => asset('imgs/signs/gemini.png')
            ],
            [
                'name' => "Cancer",
                'period' => "Jun 21 - Jul 22",
                // 'image' => asset('imgs/signs/cancer.png')
            ],
            [
                'name' => "Leo",
                'period' => "Jul 23 - Aug 22",
                // 'image' => asset('imgs/signs/leo.png')
            ],
            [
                'name' => "Virgo",
                'period' => "Aug 23 - Sep 22",
                // 'image' => asset('imgs/signs/virgo.png')
            ],
            [
                'name' => "Libra",
                'period' => "Sep 23 - Oct 23",
                // 'image' => asset('imgs/signs/libre.png')
            ],
            [
                'name' => "Scorpius",
                'period' => "Oct 24 - Nov 21",
                // 'image' => asset('imgs/signs/scorpio.png')
            ],
            [
                'name' => "Sagittarius",
                'period' => "Nov 22 - Dec 21",
                // 'image' => asset('imgs/signs/sagittarius.png')
            ],
            [
                'name' => "Capricornus",
                'period' => "Dec 22 - Jan 19",
                // 'image' => asset('imgs/signs/capricorn.png')
            ],
            [
                'name' => "Aquarius",
                'period' => "Jan 20 - Feb 18",
                // 'image' => asset('imgs/signs/aquarius.png')
            ],
            [
                'name' => "Pisces",
                'period' => "Feb 19 - Mar 20",
                // 'image' => asset('imgs/signs/pisces.png')
            ],
        ];

        foreach ($zodiacs as $zodiac) {
            Zodiac::firstOrCreate([
                'slug' => Str::slug($zodiac['name'])
            ], [
                'slug' => Str::slug($zodiac['name']),
                'name' => $zodiac['name'],
                'period' => $zodiac['period'],
            ]);
        }
    }
}
