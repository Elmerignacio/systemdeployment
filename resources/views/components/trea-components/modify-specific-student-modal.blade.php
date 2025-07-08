
<!-- Modify Confirmation Modal -->
<div id="modifyModal" class="fixed inset-0 flex items-center justify-center hidden z-50 bg-black bg-opacity-40">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
            </svg>
            <p class="text-lg text-center text-gray-800 font-semibold">
                Are you sure you want to modify this user?
            </p>
            <div class="flex justify-center space-x-4 mt-2">
                <button type="button"
                        class="cancelBTN bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                    Cancel
                </button>
                <button type="submit" value="modify" name="action" 
                        class="confirmBtn bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-md font-medium transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('modifyBTN')?.addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById('modifyModal').classList.remove('hidden');
    });

    // Cancel Button Hides the Modal
    document.querySelector('.cancelBTN')?.addEventListener('click', function () {
        document.getElementById('modifyModal').classList.add('hidden');
    });

    // Confirm Button Submits the Form
    document.querySelector('.confirmBtn')?.addEventListener('click', function () {
        document.querySelector('form').submit();
    });
</script>