<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Content') }}
        </h2>
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
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg font-semibold">Pages</div>
                            <div class="mt-1 text-sm text-gray-600">Edit the content the SPA fetches via <code>/api/pages/{slug}</code>.</div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium text-gray-700">Slug</th>
                                    <th class="px-4 py-3 font-medium text-gray-700">Title</th>
                                    <th class="px-4 py-3 font-medium text-gray-700">Published</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($pages as $page)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $page->slug }}</td>
                                    <td class="px-4 py-3">{{ $page->title }}</td>
                                    <td class="px-4 py-3">{{ $page->is_published ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="text-indigo-600 hover:text-indigo-700" href="{{ route('admin.pages.edit', $page) }}">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>