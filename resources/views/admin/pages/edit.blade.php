<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Page: {{ $page->title }}
            </h2>
            <a class="text-sm text-gray-600 hover:text-gray-800" href="{{ route('admin.pages.index') }}">← Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="slug" value="Slug" />
                            <x-text-input id="slug" class="mt-1 block w-full" type="text" value="{{ $page->slug }}" disabled />
                            <div class="mt-1 text-xs text-gray-500">The SPA home fetches <code>/api/pages/home</code> (slug <code>home</code>).</div>
                        </div>

                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" type="text" value="{{ old('title', $page->title) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="flex items-center gap-3">
                            <input id="is_published" name="is_published" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_published', $page->is_published) ? 'checked' : '' }} />
                            <label for="is_published" class="text-sm text-gray-700">Published</label>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>Save</x-primary-button>
                            <a class="text-sm text-indigo-600 hover:text-indigo-700" href="/api/pages/{{ $page->slug }}" target="_blank" rel="noreferrer">View JSON</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg font-semibold">Sections</div>
                            <div class="mt-1 text-sm text-gray-600">These are rendered as scroll sections in the SPA.</div>
                        </div>
                        <a class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" href="{{ route('admin.sections.create', $page) }}">
                            Add section
                        </a>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium text-gray-700">Order</th>
                                    <th class="px-4 py-3 font-medium text-gray-700">Anchor</th>
                                    <th class="px-4 py-3 font-medium text-gray-700">Nav</th>
                                    <th class="px-4 py-3 font-medium text-gray-700">Heading</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($page->sections as $section)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $section->sort_order }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $section->anchor }}</td>
                                    <td class="px-4 py-3">{{ $section->nav_label }}</td>
                                    <td class="px-4 py-3">{{ $section->heading }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="text-indigo-600 hover:text-indigo-700" href="{{ route('admin.sections.edit', [$page, $section]) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.sections.destroy', [$page, $section]) }}" class="inline" onsubmit="return confirm('Delete this section?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No sections yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>