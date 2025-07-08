<!-- Denomination Modal -->
<div id="DenominationModal"
    class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-green-800 text-white rounded-lg shadow-xl w-full max-w-xl relative">
        <div id="modalHeader" class="p-4 border-b border-white flex justify-between">
            <div id="selectedDate" class="font-bold text-lg bg-green-900 px-3 py-1 rounded"></div>
            <div class="font-bold text-lg bg-green-900 px-3 py-1 rounded" x-text="'Total amount: ₱' + getTotalPaid()">
            </div>
            <button id="closeModalButton" class="text-white text-xl">&times;</button>
        </div>

        <div class="bg-white text-black px-6 py-4 overflow-auto">
            <form id="denominationForm" action="{{ route('denomination.store') }}" method="POST">
                @csrf
                <input type="hidden" id="selectedDatesInput" name="selectedDates" value="2025-04-09">
                <input type="hidden" name="date" id="hiddenDateInput">

                <table class="w-full table-auto border border-black text-center">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-2 px-3 border border-black">DENOMINATION</th>
                            <th class="py-2 px-3 border border-black">QTY</th>
                            <th class="py-2 px-3 border border-black">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="denominationRows"></tbody>
                    <tr class="bg-green-700 text-white font-bold">
                        <td class="py-2 px-3 border border-black">TOTAL</td>
                        <td class="border border-black"></td>
                        <td id="totalAmountCell" class="border border-black">₱0.00</td>
                    </tr>
                </table>

                <div class="mt-4 text-center">
                    <button
                        type="button"
                        id="confirm"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-md cursor-not-allowed"
                        title="Select at least one student to enable"
                        disabled>
                        CONFIRM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmpayment" class="fixed inset-0 flex items-center justify-center bg-black/40 z-50 hidden">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
            </svg>
            <p class="text-lg text-center text-gray-800 font-semibold">
                Are you sure you want to confirm this remittance?
            </p>
            <div class="flex justify-center space-x-4 mt-2">
                <button type="button"
                    class="cancelBtn bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                    CANCEL
                </button>
                <button type="button"
                    class="confirmBtn bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-md font-medium transition">
                    CONFIRM
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successpayment" class="fixed inset-0 flex items-center justify-center bg-black/40 z-50 hidden">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none" />
            </svg>
            <p class="text-lg text-center text-green-700 font-semibold">
                Remittance confirmed. Awaiting admin approval.
            </p>
            <div class="flex justify-center mt-2">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium transition">
                    CONTINUE
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script Section -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('selectedyear');
    const remitButton = document.getElementById('remitButton');
    const hiddenDateInput = document.getElementById('hiddenDateInput');
    const confirmButton = document.getElementById('confirm');
    const form = document.getElementById('denominationForm');

    const denominations = [
        { value: 1000, name: 'thousand' },
        { value: 500, name: 'five_hundred' },
        { value: 200, name: 'two_hundred' },
        { value: 100, name: 'one_hundred' },
        { value: 50, name: 'fifty' },
        { value: 20, name: 'twenty' },
        { value: 10, name: 'ten' },
        { value: 5, name: 'five' },
        { value: 1, name: 'one' },
        { value: 0.25, name: 'twenty_five_cents' }
    ];

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: '2-digit' };
        return date.toLocaleDateString('en-US', options);
    }

    remitButton.addEventListener('click', function () {
        const selectedDate = dateInput.value;
        if (!selectedDate) {
            dateInput.style.borderColor = 'orange';
            return;
        }

        hiddenDateInput.value = selectedDate;
        document.getElementById('selectedDate').innerText = 'Date: ' + formatDate(selectedDate);

        const rows = denominations.map(d => `
            <tr>
                <td class="py-2 px-3 border border-black">₱${d.value.toFixed(2)}</td>
                <td class="border border-black">
                    <input type="number" name="${d.name}" class="w-20 p-1" min="0" data-denomination="${d.value}">
                </td>
                <td class="border border-black" data-amount="0">₱0.00</td>
            </tr>
        `).join('');
        document.getElementById('denominationRows').innerHTML = rows;

        confirmButton.disabled = true;
        confirmButton.classList.add('cursor-not-allowed');
        confirmButton.title = "Select at least one student to enable";

        document.getElementById('DenominationModal').classList.remove('hidden');
    });

    document.getElementById('closeModalButton').addEventListener('click', function () {
        document.getElementById('DenominationModal').classList.add('hidden');
    });

    document.getElementById('DenominationModal').addEventListener('input', function (e) {
        if (e.target.matches('input[type="number"]')) {
            const denom = parseFloat(e.target.dataset.denomination);
            const qty = parseInt(e.target.value) || 0;
            const amt = denom * qty;
            e.target.closest('tr').querySelector('td[data-amount]').innerText = '₱' + amt.toFixed(2);
            updateTotalAmount();
            checkIfAnyQtyInput();
        }
    });

    function updateTotalAmount() {
        const totalCells = document.querySelectorAll('td[data-amount]');
        let total = 0;
        totalCells.forEach(cell => {
            total += parseFloat(cell.innerText.replace('₱', '')) || 0;
        });
        document.getElementById('totalAmountCell').innerText = '₱' + total.toFixed(2);
    }

    function checkIfAnyQtyInput() {
        const inputs = document.querySelectorAll('#denominationRows input[type="number"]');
        let hasValue = false;
        inputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                hasValue = true;
            }
        });
        confirmButton.disabled = !hasValue;
        confirmButton.classList.toggle('cursor-not-allowed', !hasValue);
        confirmButton.classList.toggle('cursor-pointer', hasValue);
        confirmButton.title = hasValue ? "" : "Select at least one student to enable";
    }

    // Modal triggers
    const confirmpayment = document.getElementById("confirmpayment");
    const successpayment = document.getElementById("successpayment");
    const confirmTrigger = document.getElementById("confirm");
    const confirmModalBtn = confirmpayment?.querySelector(".confirmBtn");
    const cancelModalBtn = confirmpayment?.querySelector(".cancelBtn");
    const successConfirmBtn = successpayment?.querySelector("button[type='submit']");

    confirmTrigger?.addEventListener("click", function () {
        if (!confirmButton.disabled) {
            confirmpayment?.classList.remove("hidden");
        }
    });

    confirmModalBtn?.addEventListener("click", function () {
        confirmpayment?.classList.add("hidden");
        successpayment?.classList.remove("hidden");
    });

    cancelModalBtn?.addEventListener("click", function () {
        confirmpayment?.classList.add("hidden");
    });

    successConfirmBtn?.addEventListener("click", function () {
        successpayment?.classList.add("hidden");
        form.submit();
    });
});
</script>
