@props(['itemType', 'itemId', 'itemName', 'defaultSubject' => '', 'defaultContent' => ''])

<button onclick="showEmailShareModal('{{ $itemType }}', {{ $itemId }}, '{{ addslashes($itemName) }}', '{{ addslashes($defaultSubject) }}', '{{ addslashes($defaultContent) }}')" 
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700">
    <i class="fas fa-envelope mr-2"></i>Share via Email
</button>

<!-- Email Share Modal -->
<div id="emailShareModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-black">Share via Email</h2>
            <button onclick="closeEmailShareModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="emailShareForm" action="{{ route('email.share') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="share_item_type" name="item_type">
            <input type="hidden" id="share_item_id" name="item_id">

            <div class="space-y-4">
                <div>
                    <label for="recipients" class="block text-sm font-medium text-black mb-1">
                        Recipients (comma-separated emails) *
                    </label>
                    <input type="text" id="recipients" name="recipients" required
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"
                           placeholder="email1@example.com, email2@example.com">
                    <p class="mt-1 text-xs text-gray-500">Enter email addresses separated by commas</p>
                    @error('recipients')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-black mb-1">Subject *</label>
                    <input type="text" id="subject" name="subject" required
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-black mb-1">Message Content *</label>
                    <textarea id="content" name="content" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="attachments" class="block text-sm font-medium text-black mb-1">Attachments</label>
                    <input type="file" id="attachments" name="attachments[]" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    <p class="mt-1 text-xs text-gray-500">PDF, Word, Excel, Images (max 10MB each)</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeEmailShareModal()" 
                        class="px-6 py-2 border border-gray-300 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                    <i class="fas fa-paper-plane mr-2"></i>Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showEmailShareModal(itemType, itemId, itemName, defaultSubject, defaultContent) {
    document.getElementById('share_item_type').value = itemType;
    document.getElementById('share_item_id').value = itemId;
    document.getElementById('subject').value = defaultSubject || `Shared: ${itemName}`;
    document.getElementById('content').value = defaultContent || `Please find attached: ${itemName}`;
    document.getElementById('emailShareModal').classList.remove('hidden');
}

function closeEmailShareModal() {
    document.getElementById('emailShareModal').classList.add('hidden');
    document.getElementById('emailShareForm').reset();
}

// Close modal on outside click
document.getElementById('emailShareModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmailShareModal();
    }
});
</script>

