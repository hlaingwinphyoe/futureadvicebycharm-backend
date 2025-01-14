<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['Relationship'],
            ['Business'],
            ['Work'],
            ['Social'],
        ];

        foreach ($categories as $category) {
            foreach ($category as $name) {
                $slug = Str::slug($name);
                $category = Category::create([
                    'slug' => $slug,
                    'name' => $name,
                ]);
            }
        }

        Post::factory()->count(20)->create();
    }
}
