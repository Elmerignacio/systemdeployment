   <!-- Confirm Delete Modal -->
        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center hidden z-50 bg-black bg-opacity-40">
            <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-red-700 transition-all duration-300">
                <div class="flex flex-col items-center space-y-6">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
                    </svg>
                    <p class="text-lg text-center text-gray-800 font-semibold">
                        Are you sure you want to delete this payable?
                    </p>
                    <div class="flex justify-center space-x-4 mt-2">
                        <button type="button"
                                class="cancelBtn bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition"
                                @click="document.getElementById('deleteModal').classList.add('hidden')">
                            Cancel
                        </button>
                        <form method="POST" x-ref="dynamicDeleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="confirmBtn bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-md font-medium transition">
                                Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        