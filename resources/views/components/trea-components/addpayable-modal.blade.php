<!-- Archive Confirmation Modal -->
<div id="Add" class="fixed inset-0 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
            </svg>
            <p class="text-lg text-center text-gray-800 font-semibold">
                Are you sure you want to add this payable?
            </p>
            <div class="flex justify-center space-x-4 mt-2">
                <button type="button"
                        class="cancelBtn bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                    Cancel
                </button>
                <button type="button"
                        class="confirmBtn bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-md font-medium transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="Confirmation" class="fixed inset-0 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none" />
            </svg>

            <p class="text-lg text-center text-green-700 font-semibold">
                New payable has been successfully added!
            </p>
            <div class="flex justify-center mt-2">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const archiveModal = document.getElementById("Add"); 
    const successModal = document.getElementById("Confirmation");
    const confirmButton = archiveModal.querySelector(".confirmBtn");
    const cancelButton = archiveModal.querySelector(".cancelBtn");
    const successConfirmButton = successModal.querySelector("button[type='submit']");
    const addUserBtn = document.getElementById("addUserBtn");
    const form = document.getElementById("userForm");

    addUserBtn.addEventListener("click", function () {
        if (form.checkValidity()) {
            archiveModal.classList.remove("hidden");
        } else {
            form.reportValidity(); 
        }
    });

    confirmButton.addEventListener("click", function () {
        archiveModal.classList.add("hidden");
        successModal.classList.remove("hidden");
    });

    cancelButton.addEventListener("click", function () {
        archiveModal.classList.add("hidden");
    });
    
    successConfirmButton.addEventListener("click", function () {
        successModal.classList.add("hidden");
        form.submit();
    });
});
</script>
