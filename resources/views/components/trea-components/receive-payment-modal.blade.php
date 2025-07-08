<!-- ✅ Confirmation Modal -->
<div id="receive" class="fixed inset-0 flex items-center justify-center hidden z-50 bg-black bg-opacity-40">
  <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
    <div class="flex flex-col items-center space-y-6">
      <!-- Icon -->
      <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
      </svg>

      <!-- Confirmation Message -->
      <p class="text-lg text-center text-gray-800 font-semibold">
        Are you sure you want to receive this payment?
      </p>

      <!-- Action Buttons -->
      <div class="flex justify-center space-x-4 mt-2">
        <button type="button"
                class="cancelBtn bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
          Cancel
        </button>

        <!-- ✅ Confirm Button -->
        <button type="button" id="confirmPaymentBtn"
                class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-md font-medium transition">
          Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ✅ JavaScript Section -->
<script>
  // Show confirmation modal
  document.getElementById('receiveBtn')?.addEventListener('click', function () {
    const form = document.getElementById('paymentForm');

    // Validate form before showing confirmation
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    // Validate at least one valid amount
    const totalAmountPaidText = document.getElementById('totalAmountPaid').textContent.trim();
    const paidAmount = parseFloat(totalAmountPaidText.replace(/[₱,]/g, ''));

    if (isNaN(paidAmount) || paidAmount <= 0) {
      alert('Please enter at least one amount to pay before submitting.');
      return;
    }

    // Show modal
    document.getElementById('receive').classList.remove('hidden');
  });

  // Cancel button
  document.querySelector('.cancelBtn')?.addEventListener('click', function () {
    document.getElementById('receive').classList.add('hidden');
  });

  // ✅ Confirm payment (safe single click)
  document.getElementById('confirmPaymentBtn')?.addEventListener('click', function () {
    const btn = this;

    // Disable to prevent double click
    btn.disabled = true;
    btn.textContent = 'Processing...';

    // Submit the form
    document.getElementById('paymentForm').submit();
  });
</script>
