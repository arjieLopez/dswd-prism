<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('user')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Upload Scanned Purchase Request') }}
                    </h2>
                    <a href="{{ route('user.requests') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        ← Back to Requests
                    </a>
                </div>

                <form method="post" action="{{ route('uploaded-documents.store') }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="pr_number" :value="__('PR Number')" />
                        <x-text-input id="pr_number" name="pr_number" type="text" class="mt-1 block w-full"
                            :value="old('pr_number', $prNumber ?? '')" required autofocus readonly />
                        <x-input-error :messages="$errors->get('pr_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="scanned_copy" :value="__('Scanned Copy')" />
                        <div x-data="{ fileName: null, isDragging: false }" @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name">

                            <!-- File Upload Area -->
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition-colors"
                                :class="isDragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300'">

                                <!-- Show when no file is selected -->
                                <div x-show="!fileName" class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="scanned_copy"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="scanned_copy" name="scanned_copy" type="file" class="sr-only"
                                                x-ref="fileInput" accept=".pdf,.jpg,.jpeg,.png" required
                                                @change="fileName = $event.target.files[0]?.name">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, JPG, JPEG, PNG up to 10MB
                                    </p>
                                </div>

                                <!-- Show when file is selected -->
                                <div x-show="fileName" class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        <p class="font-medium text-green-600" x-text="fileName"></p>
                                        <p class="text-xs text-gray-500">File selected successfully</p>
                                    </div>
                                    <button type="button" @click="fileName = null; $refs.fileInput.value = ''"
                                        class="text-xs text-red-600 hover:text-red-800">
                                        Remove file
                                    </button>
                                </div>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('scanned_copy')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm placeholder-gray-400"
                            placeholder="Add any additional notes...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Check the implementation of the button --}}
                        <x-primary-button>{{ __('Upload Scanned Copy') }}</x-primary-button>
                        <a href="{{ route('user.requests') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-page-layout>
