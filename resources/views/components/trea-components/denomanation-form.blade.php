<form id="confirmDenominationForm" method="POST" action="/treasurer/update-remittance-status">
    @csrf
    <input type="hidden" name="date_remitted" id="form_date_remitted">
    <input type="hidden" name="collected_by" id="form_collected_by">
    

    <div id="ConfirmDenomitationModal" class="fixed inset-0 bg-[#1a4d2e] bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#1a4d2e] text-white rounded-lg shadow-xl w-full max-w-xl relative">
            <div class="p-4 border-b border-white flex justify-between">
                <div id="date_remitted" class="font-bold text-lg bg-green-900 px-3 py-1 rounded"></div>
                <div id="totalAmountText" class="font-bold text-lg bg-green-900 px-3 py-1 rounded"></div>
                <div id="collectedBy" class="hidden font-bold text-lg bg-green-900 px-3 py-1 rounded"></div>

                <button type="button" id="closeModalButton" class="text-white text-xl">&times;</button>
            </div>

            <div class="bg-white text-black px-6 py-4 overflow-auto">
                <table class="w-full table-auto border border-black text-center">
                    <thead class="bg-[#1a4d2e] text-white">
                        <tr>
                            <th class="py-2 px-3 border border-black">DENOMINATION</th>
                            <th class="py-2 px-3 border border-black">QTY</th>
                            <th class="py-2 px-3 border border-black">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="denominationRows"></tbody>
                    <tr class="bg-[#1a4d2e] text-white font-bold">
                        <td class="py-2 px-3 border border-black">TOTAL</td>
                        <td class="border border-black"></td>
                        <td id="remitTotalAmountCell" class="border border-black">₱0.00</td>
                    </tr>
                </table>
                
                <div class="mt-4">
                    <button type="submit" class="bg-[#1a4d2e] hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-md cursor-pointer ">
                       CONFIRM
                    </button>
                </div>
            </div>
        </div>  
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const remitButton = document.getElementById('remitButton');
    const dateInput = document.getElementById('selectedyear'); 
    const collectedByInput = document.getElementById('collectedby');

    remitButton.addEventListener('click', function () {
        const collectedBy = collectedByInput.value;  
        const date_remitted = dateInput.value;  

        if (!date_remitted || !collectedBy) {
            return;
        }

        fetch(`/treasurer/get-denomination?date=${date_remitted}&collectedBy=${collectedBy}`)
            .then(response => response.json())
            .then(data => {
                if (!data || !data.success) {
                    return;
                }

                document.getElementById('date_remitted').innerText = new Date(data.denomination.date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });              
            document.getElementById('collectedBy').innerText = data.denomination.collectedBy;

                // Populate hidden form inputs
                document.getElementById('form_date_remitted').value = data.denomination.date;
                document.getElementById('form_collected_by').value = data.denomination.collectedBy;

                const rows = [
                    { label: '₱1000', qty: data.denomination.thousand, amount: 1000 },
                    { label: '₱500', qty: data.denomination.five_hundred, amount: 500 },
                    { label: '₱200', qty: data.denomination.two_hundred, amount: 200 },
                    { label: '₱100', qty: data.denomination.one_hundred, amount: 100 },
                    { label: '₱50', qty: data.denomination.fifty, amount: 50 },
                    { label: '₱20', qty: data.denomination.twenty, amount: 20 },
                    { label: '₱10', qty: data.denomination.ten, amount: 10 },
                    { label: '₱5', qty: data.denomination.five, amount: 5 },
                    { label: '₱1', qty: data.denomination.one, amount: 1 },
                    { label: '₱0.25', qty: data.denomination.twenty_five_cents, amount: 0.25 },
                ];

                let total = 0;
                const rowHTML = rows.map(r => {
                    const amt = r.qty * r.amount;
                    total += amt;
                    return `
                        <tr>
                            <td class="py-2 px-3 border border-black">${r.label}</td>
                            <td class="py-2 px-3 border border-black">${r.qty}</td>
                            <td class="py-2 px-3 border border-black">₱${amt.toFixed(2)}</td>
                        </tr>
                    `;
                }).join('');

                document.getElementById('denominationRows').innerHTML = rowHTML;
                document.getElementById('remitTotalAmountCell').innerText = '₱' + total.toFixed(2);
                document.getElementById('totalAmountText').innerText = 'Total amount: ₱' + total.toFixed(2);

                document.getElementById('ConfirmDenomitationModal').classList.remove('hidden');
            });
    });

    document.getElementById('closeModalButton').addEventListener('click', function () {
        document.getElementById('ConfirmDenomitationModal').classList.add('hidden');
    });
});

</script>


<div id="RemitDenomitationModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-[#1a4d2e] text-white rounded-lg shadow-xl w-full max-w-xl relative">
        <div id="selectedDate" class="hidden font-bold text-lg bg-green-900 px-3 py-1 rounded"></div>

        <form action="{{ route('store.denomination') }}" method="POST">
            @csrf
            <div id="modalHeader" class="p-4 border-b border-white flex justify-between">
                <div class="flex items-center space-x-2 mt-2">
           <input type="date" id="year" name="date"
        class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500 text-black"
        required>

      <script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('year');
        if (dateInput) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${yyyy}-${mm}-${dd}`;
            dateInput.value = formattedDate;
        }
    });
</script>


                </div>
                <div class="font-bold text-lg bg-green-900 px-3 py-1 rounded" x-text="'Total amount: ₱' + getTotalPaid()"></div>
                <button id="closeRemitModal" type="button" class="text-white text-xl">&times;</button>
            </div>


            <div class="bg-white text-black px-6 py-4 overflow-auto">
                <input type="hidden" name="selectedDateForRequest" x-model="selectedDateForRequest">
                <div type="hidden" name="date" id="hiddenDateInput"></div>

                <table class="w-full table-auto border border-black text-center">
                    <thead class="bg-[#1a4d2e] text-white">
                        <tr>
                            <th class="py-2 px-3 border border-black">DENOMINATION</th>
                            <th class="py-2 px-3 border border-black">QTY</th>
                            <th class="py-2 px-3 border border-black">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="denominations"></tbody>
                    <tr class="bg-[#1a4d2e] text-white font-bold">
                        <td class="py-2 px-3 border border-black">TOTAL</td>
                        <td class="border border-black"></td>
                     <td id="remitTotalAmountCollect" class="border border-black">₱0.00</td>

                    </tr>
                </table>

                <div class="mt-4 text-center">
                    <button type="button" id="confirm" 
                        class="bg-[#1a4d2e] hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-md cursor-pointer" 
                        x-bind:disabled="!date_remitted" 
                        title="Select a date to enable">
                        CONFIRM
                    </button>
                </div>
            </div>
    
    </div>
</div>


<script>
    var status = 'RECEIVED BY TREASURER'; 

    function handleRemitButtonClick() {
        var button = document.getElementById("remitButton");
        var selectedDate = document.getElementById("year").value;

        if (status === 'RECEIVED BY TREASURER' && !selectedDate) {
            return; 
        }

        if (button.innerText === 'CONFIRM') {
            document.getElementById('ConfirmDenomitationModal').classList.remove('hidden');
        } else if (button.innerText === 'REMIT') {
            document.getElementById('RemitDenomitationModal').classList.remove('hidden');
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function confirmDenomination() {
        alert("Denomination confirmed");

        if (status === 'PENDING') {
            closeModal('ConfirmDenomitationModal');
        } else if (status === 'RECEIVED BY TREASURER') {
            closeModal('RemitDenomitationModal');
        }
    }
</script>
<script> 
    document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('year');
    const remitButton = document.getElementById('remitButton');
    const hiddenDateInput = document.getElementById('hiddenDateInput');
    const confirmButton = document.getElementById('confirm');

    confirmButton.disabled = true; 
    confirmButton.title = "Select at least one student to enable"; 
    confirmButton.classList.add('cursor-not-allowed');

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

        const formattedDate = formatDate(selectedDate);
        hiddenDateInput.value = selectedDate;
        document.getElementById('selectedDate').innerText = 'Date: ' + formattedDate;

        const rows = denominations.map(d => `
            <tr>
                <td class="py-2 px-3 border border-black">₱${d.value.toFixed(2)}</td>
                <td class="border border-black">
                    <input type="number" name="${d.name}" class="w-20 p-1" min="0" data-denomination="${d.value}">
                </td>
                <td class="border border-black" data-amount="0">₱0.00</td>
            </tr>
        `).join('');
        document.getElementById('denominations').innerHTML = rows;

        confirmButton.disabled = true; 
        confirmButton.title = "Select at least one student to enable"; 
        confirmButton.classList.add('cursor-not-allowed'); 
    });


    document.getElementById('RemitDenomitationModal').addEventListener('input', function (e) {
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
            const cellValue = parseFloat(cell.innerText.replace('₱', '').replace(',', '')) || 0; 
            total += cellValue;
        });
        document.getElementById('remitTotalAmountCollect').innerText = '₱' + total.toFixed(2);
    }

    function checkIfAnyQtyInput() {
        const inputs = document.querySelectorAll('#denominations input[type="number"]');
        let hasValue = false;
        inputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                hasValue = true;
            }
        });
        confirmButton.disabled = !hasValue;

        if (confirmButton.disabled) {
            confirmButton.classList.add('cursor-not-allowed'); 
            confirmButton.title = "Select at least one student to enable";
        } else {
            confirmButton.classList.remove('cursor-not-allowed'); 
            confirmButton.classList.add('cursor-pointer'); 
            confirmButton.title = "";
        }
    }
});
document.addEventListener('DOMContentLoaded', function () {
                    const closeButtons = document.querySelectorAll('button[type="button"].text-xl');
                    const modal = document.getElementById('RemitDenomitationModal');
            

                    closeButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            modal.classList.add('hidden'); 
                        });
                    });
                });



</script>







