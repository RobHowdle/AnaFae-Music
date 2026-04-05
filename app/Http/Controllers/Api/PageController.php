<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('sections')
            ->firstOrFail();

        return response()->json([
            'id' => $page->id,
            'slug' => $page->slug,
            'title' => $page->title,
            'sections' => $page->sections->map(fn($section) => [
                'id' => $section->id,
                'anchor' => $section->anchor,
                'nav_label' => $section->nav_label,
                'heading' => $section->heading,
                'body' => $section->body,
                'background_image_url' => $section->background_image_url,
                'background_position' => $section->background_position,
                'sort_order' => $section->sort_order,
                'cta_label' => $section->cta_label,
                'cta_url' => $section->cta_url,
            ]),
        ]);
    }
}
