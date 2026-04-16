<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\HomePageDefaultsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageDefaultsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_home_page_defaults_when_missing(): void
    {
        $this->seed(HomePageDefaultsSeeder::class);

        $home = Page::query()->where('slug', 'home')->first();

        $this->assertNotNull($home);
        $this->assertSame('Home', $home->title);
        $this->assertTrue($home->is_published);
        $this->assertCount(4, $home->sections);
    }

    public function test_it_does_not_overwrite_an_existing_home_page(): void
    {
        $home = Page::query()->create([
            'slug' => 'home',
            'title' => 'Custom Home Title',
            'is_published' => false,
        ]);

        $this->seed(HomePageDefaultsSeeder::class);

        $home->refresh();

        $this->assertSame('Custom Home Title', $home->title);
        $this->assertFalse($home->is_published);
        $this->assertCount(4, $home->sections);
    }
}
