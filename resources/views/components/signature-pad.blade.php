@props(['name' => 'signature', 'label' => 'Signature'])

<div class="space-y-4">
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    
    <div class="space-y-4">
        <!-- File Upload Option -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">Upload Signature Image</label>
            <input type="file" 
                   name="signature_file" 
                   accept=".png"
                   class="block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-md file:border-0
                          file:text-sm file:font-semibold
                          file:bg-yellow-50 file:text-yellow-700
                          hover:file:bg-yellow-100
                          border border-gray-300 rounded-md">
            <p class="mt-1 text-sm text-gray-500">Upload a PNG file of your signature (transparent background recommended)</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="signatory_name" class="block text-sm font-medium text-gray-700">Signatory Name</label>
            <input type="text" 
                   name="signatory_name" 
                   id="signatory_name" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
        </div>
        
        <div>
            <label for="signatory_designation" class="block text-sm font-medium text-gray-700">Designation</label>
            <input type="text" 
                   name="signatory_designation" 
                   id="signatory_designation" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
        </div>
    </div>
</div> 