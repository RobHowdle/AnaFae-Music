<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Section: {{ $section->nav_label }}
            </h2>
            <a class="text-sm text-gray-600 hover:text-gray-800" href="{{ route('admin.pages.edit', $page) }}">← Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.sections.update', [$page, $section]) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="anchor" value="Anchor (used as #id)" />
                                <x-text-input id="anchor" name="anchor" class="mt-1 block w-full" type="text" value="{{ old('anchor', $section->anchor) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('anchor')" />
                            </div>
                            <div>
                                <x-input-label for="sort_order" value="Sort order" />
                                <x-text-input id="sort_order" name="sort_order" class="mt-1 block w-full" type="number" value="{{ old('sort_order', $section->sort_order) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="nav_label" value="Nav label" />
                            <x-text-input id="nav_label" name="nav_label" class="mt-1 block w-full" type="text" value="{{ old('nav_label', $section->nav_label) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nav_label')" />
                        </div>

                        <div>
                            <x-input-label for="heading" value="Heading" />
                            <x-text-input id="heading" name="heading" class="mt-1 block w-full" type="text" value="{{ old('heading', $section->heading) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('heading')" />
                        </div>

                        <div>
                            <x-input-label for="body" value="Body (HTML allowed)" />
                            <textarea id="body" name="body" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="6">{{ old('body', $section->body) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('body')" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="background_image" value="Background image upload" />
                                <input id="background_image" name="background_image" type="file" accept="image/*" class="mt-1 block w-full text-sm" />
                                <x-input-error class="mt-2" :messages="$errors->get('background_image')" />
                                @if ($section->background_image_url)
                                <div class="mt-2 text-xs text-gray-600">
                                    Current: <a class="text-indigo-600 hover:text-indigo-700" href="{{ $section->background_image_url }}" target="_blank" rel="noreferrer">preview</a>
                                </div>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="background_position" value="Background position" />
                                <x-text-input id="background_position" name="background_position" class="mt-1 block w-full" type="text" value="{{ old('background_position', $section->background_position) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('background_position')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="background_image_path" value="Background image URL/path (optional)" />
                            <x-text-input id="background_image_path" name="background_image_path" class="mt-1 block w-full" type="text" value="{{ old('background_image_path', $section->background_image_path) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('background_image_path')" />
                            <div class="mt-1 text-xs text-gray-500">If you upload an image, it will override this field.</div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <x-input-label for="cta_label" value="CTA label" />
                                <x-text-input id="cta_label" name="cta_label" class="mt-1 block w-full" type="text" value="{{ old('cta_label', $section->cta_label) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('cta_label')" />
                            </div>
                            <div>
                                <x-input-label for="cta_url" value="CTA URL" />
                                <x-text-input id="cta_url" name="cta_url" class="mt-1 block w-full" type="text" value="{{ old('cta_url', $section->cta_url) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('cta_url')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>Save section</x-primary-button>
                            <a class="text-sm text-gray-600 hover:text-gray-800" href="{{ route('admin.pages.edit', $page) }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>