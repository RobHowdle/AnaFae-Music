<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageSectionRequest;
use App\Http\Requests\Admin\UpdatePageSectionRequest;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function create(Page $page): View
    {
        return view('admin.sections.create', [
            'page' => $page,
        ]);
    }

    public function store(StorePageSectionRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $data['page_id'] = $page->id;

        if ($request->hasFile('background_image')) {
            $data['background_image_path'] = $request->file('background_image')
                ->store('page-sections', 'public');
        }

        $section = PageSection::query()->create($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', "Section '{$section->nav_label}' created.");
    }

    public function edit(Page $page, PageSection $section): View
    {
        abort_unless($section->page_id === $page->id, 404);

        return view('admin.sections.edit', [
            'page' => $page,
            'section' => $section,
        ]);
    }

    public function update(UpdatePageSectionRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        $data = $request->validated();

        if ($request->hasFile('background_image')) {
            if ($section->background_image_path && !str_starts_with($section->background_image_path, 'http')) {
                Storage::disk('public')->delete($section->background_image_path);
            }

            $data['background_image_path'] = $request->file('background_image')
                ->store('page-sections', 'public');
        }

        $section->update($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', "Section '{$section->nav_label}' updated.");
    }

    public function destroy(Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        if ($section->background_image_path && !str_starts_with($section->background_image_path, 'http')) {
            Storage::disk('public')->delete($section->background_image_path);
        }

        $label = $section->nav_label;
        $section->delete();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', "Section '{$label}' deleted.");
    }
}
