@props(['model' => null])

<div class="md:col-span-2">
    <x-input-label for="attachments" :value="__('File Attachments')" />
    <input id="attachments" name="attachments[]" type="file" multiple 
        class="mt-1 block w-full" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" />
    <p class="mt-1 text-sm text-gray-500">
        Allowed file types: Images (JPG, JPEG, PNG, GIF), Documents (PDF, DOC, DOCX). Maximum file size: 2MB.
    </p>
    <x-input-error class="mt-2" :messages="$errors->get('attachments.*')" />

    @if($model && $model->attachments)
        <div class="mt-4">
            <h4 class="font-medium text-gray-900">Current Attachments:</h4>
            <div class="mt-2 space-y-2">
                @foreach($model->attachments as $path)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ basename($path) }}</span>
                            <a href="{{ asset('storage/' . $path) }}" target="_blank" 
                                class="text-yellow-600 hover:text-yellow-900 text-sm">View</a>
                        </div>
                        <div class="flex items-center">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="delete_attachments[]" value="{{ $path }}"
                                    class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Delete</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div> 