<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var \App\Models\Page $page */
        $page = $this->route('page');
        /** @var \App\Models\PageSection $section */
        $section = $this->route('section');

        return [
            'anchor' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z][A-Za-z0-9_-]*$/',
                Rule::unique('page_sections', 'anchor')
                    ->where('page_id', $page->id)
                    ->ignore($section->id),
            ],
            'nav_label' => ['required', 'string', 'max:255'],
            'heading' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],

            'background_image_path' => ['nullable', 'string', 'max:2048'],
            'background_position' => ['nullable', 'string', 'max:255'],
            'background_image' => ['nullable', 'image', 'max:5120'],

            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
