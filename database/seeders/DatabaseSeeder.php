<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('local')) {
            User::query()->firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }

        $home = Page::query()->updateOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'is_published' => true],
        );

        $sections = [
            [
                'anchor' => 'home',
                'nav_label' => 'Home',
                'heading' => 'Your Story, Your Day',
                'body' => 'Your Songs, Your Way',
                'background_image_path' => '/images/home_bg.jpg',
                'background_position' => 'center',
                'sort_order' => 0,
                'cta_label' => null,
                'cta_url' => null,
            ],
            [
                'anchor' => 'about',
                'nav_label' => 'About',
                'heading' => 'About',
                'body' => 'Intro copy goes here.',
                'background_image_path' => null,
                'background_position' => 'center',
                'sort_order' => 10,
                'cta_label' => null,
                'cta_url' => null,
            ],
            [
                'anchor' => 'services',
                'nav_label' => 'Services',
                'heading' => 'Services',
                'body' => 'Service overview goes here.',
                'background_image_path' => null,
                'background_position' => 'center',
                'sort_order' => 20,
                'cta_label' => null,
                'cta_url' => null,
            ],
            [
                'anchor' => 'contact',
                'nav_label' => 'Contact',
                'heading' => 'Contact',
                'body' => 'Booking, press, and general enquiries.',
                'background_image_path' => null,
                'background_position' => 'center',
                'sort_order' => 30,
                'cta_label' => 'Email',
                'cta_url' => 'mailto:hello@example.com',
            ],
        ];

        foreach ($sections as $section) {
            PageSection::query()->firstOrCreate(
                ['page_id' => $home->id, 'anchor' => $section['anchor']],
                $section,
            );
        }
    }
}
