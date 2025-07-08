<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>

<x-Add-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4" x-data="remittanceComponent()">
            <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

            <x-trea-components.nav-link>

                <a href="/admin/remitted" class="text-[17px] text-gray-600">Remittance</a>
                <a href="/admin/CashOnHand" class="text-[17px] font-semibold text-[#1a4d2e] border-b-2 border-[#1a4d2e] pb-1">Cash on hand</a>
            </x-trea-components.nav-link>

            <div class="flex flex-col md:flex-row overflow-auto">
                <div class="w-full md:w-1/2 overflow-auto">
                    <div class=" overflow-auto sm:mr-4 md:mr-6 lg:mr-8 xl:mr-10">
                        <div class="flex flex-col md:flex-row md:justify-between items-start mb-4 w-full">
                            <x-trea-components.sorting class="w-full md:w-auto" />
                            <x-trea-components.year-sorting class="w-full md:w-auto" />
                        </div>
                       <div>
                        <table class="w-full min-w-[600px] border border-black rounded-lg text-sm text-center">
                            <thead>
                                <tr class="bg-[#1a4d2e] text-white border border-black">
                                    <th class="p-2 border border-black">DATE</th>
                                    <th class="p-2 border border-black">COLLECTED BY</th>
                                    <th class="p-2 border border-black">AMOUNT</th>
                                    <th class="p-2 border border-black">STATUS</th>
                                </tr>
                            </thead>
                            <tbody x-data="{ activeRow: null }">
                                @php
                                    $totalAmount = 0;
                                    $groupedRemittances = $remittances->unique('id')->groupBy(function($remittance) {
                                        return \Carbon\Carbon::parse($remittance->date_remitted)->format('Y-m-d') . '-' . $remittance->collectedBy . '-' . $remittance->status;
                                    });
                                @endphp
                        @foreach ($groupedRemittances as $group => $remittanceGroup)
                        @php
                            $remittance = $remittanceGroup->first();
                            $payableCount = $remittanceGroup->count();
                            $descriptions = $remittanceGroup->pluck('description')->unique();
                            $totalPaid = $remittanceGroup->sum('paid');
                            $totalCollected = $remittanceGroup->sum('amountCollected');
                            $totalAmount += $totalPaid + $totalCollected;
                        @endphp

                        <tr :class="{'bg-gray-300': activeRow === '{{ $remittance->id }}'}" class="border border-black hover:bg-gray-200 cursor-pointer"
                            @click="activeRow = (activeRow === '{{ $remittance->id }}') ? null : '{{ $remittance->id }}'; openModal({
                                id: '{{ $remittance->id }}',
                                collectedBy: '{{ $remittance->collectedBy }}',
                                totalPaid: {{ $totalPaid }},
                                totalCollected: {{ $totalCollected }},
                                payableCount: {{ $payableCount }},
                                descriptions: @js($descriptions),
                                status: '{{ $remittance->status }}',
                                date_remitted: '{{ $remittance->date_remitted }}',
                                selectedDateForRequest: '{{ $remittance->formattedDate }}'
                            })">
                            <td class="p-2 border border-black">{{ \Carbon\Carbon::parse($remittance->date_remitted)->format('F d, Y') }}</td>
                            <td class="p-2 border border-black">{{ $remittance->collectedBy }}</td>
                            <td class="p-2 border border-black">
                                {{ number_format($totalPaid + $totalCollected, 2) }}
                            </td>
                            <td class="p-2 border border-black font-bold {{
                                strtoupper($remittance->status) === 'TO TREASURER' ? 'text-purple-600 font-bold drop-shadow-sm' :
                                 (strtoupper($remittance->status) === 'COLLECTED BY TREASURER' ? 'text-blue-600 font-bold drop-shadow-sm' :
                                (strtoupper($remittance->status) === 'REMITTED' ? '	text-yellow-500 font-bold drop-shadow-sm' :
                                (strtoupper($remittance->status) === 'COLLECTED' ? 'text-green-600 font-bold drop-shadow-sm' : 'text-red-600')))
                            }}">
                                {{ strtoupper($remittance->status) }}
                            </td>
                        </tr>
                    @endforeach

                            </tbody>


                            <tfoot x-data="{ totalAmount: '{{ number_format($totalAmount, 2) }}' }">
                                <tr>
                                    <td class="p-2 border border-black font-bold text-lg text-white bg-[#1a4d2e]" colspan="2">Total</td>
                                    <td class="p-2 border border-black font-bold text-lg"  x-text="totalAmount"></td>
                                </tr>
                            </tfoot>
                        </table>

                        </div>
                    </div>
                </div>

                <div
            x-show="showModal"
            x-transition:enter="transition duration-300 transform"
            x-transition:enter-start="-translate-y-10 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-10 opacity-0"
            class="w-full md:w-1/2 p-4 mt-[4%] bg-gray-400 bg-opacity-40 shadow-md border-[#1a4d2e] border-2 relative">

            <div x-show="showModal" x-transition>

              <div class="flex flex-col md:flex-row items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <p id="studentName" class="text-[25px] font-bold text-[#1a4d2e]" x-text="studentName"></p>
                    <p class="text-[18px]"><span x-text="collectorYearLevel + ' - ' + collectorBlock"></span></p>



                    <div class="flex items-center space-x-2 mt-2">
                        <input type="date" id="selectedyear" name="date"
                            x-model="date_remitted"
                            class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500" required>
                    </div>


                    <div class="flex items-center space-x-2 mt-2">
                        <input type="hidden" id="collectedby" name=""
                            x-model="collectedBy"
                            class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500" required>
                    </div>

                </div>


                <div class="w-full md:w-auto overflow-x-auto flex md:text-center md:justify-end">
                    <table class="w-full border border-black shadow-lg rounded-lg">
                        <thead>
                            <tr class="bg-gray-800 text-white text-xs md:text-base">
                                <th class="p-2 border border-black bg-[#1a4d2e]">CASH ON HAND</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white text-black text-center text-sm md:text-lg font-semibold">
                                <td class="p-2 border border-black font-bold" x-text="getTotalPaid()"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
                <!-- Payable Table -->
                <div x-show="showPayableDetails" class="mt-4">
                    <table class="w-full text-sm text-center border border-black">
                        <thead>
                            <tr class="bg-[#1a4d2e] text-white">
                                <th class="p-2 border border-black">DESCRIPTION</th>
                                <th class="p-2 border border-black">AMOUNT</th>
                                <th class="p-2 border border-black">AMOUNT PAID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="desc in descriptions" :key="desc">
                                <tr>
                                    <td class="p-2 bg-white border border-black" x-text="desc"></td>
                                    <td class="p-2 bg-white border border-black" x-text="getBalance(desc)"></td>
                                    <td class="p-2 bg-white border border-black" x-text="getPaid(desc)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="p-2 bg-[#1a4d2e] text-white border border-black font-bold" colspan="2">Total</td>
                                <td class="p-2 bg-white border border-black font-bold" x-text="getTotalPaid()"></td>
                            </tr>
                        </tfoot>
                    </table>
             
                </div>
            </div>

            <script>
                function remittanceComponent() {
                    return {
                        selectedId: null,
                        selectedDate: '',
                        selectedDateForRequest: [],
                        studentName: '',
                        totalAmount: 0,
                        payableCount: 0,
                        showModal: false,
                        showPayableDetails: false,
                        showStudentListModal: false,
                        selectedDescription: '',
                        studentList: [],
                        descriptions: [],
                        date_remitted:[],
                        collectedBy:[],
                        status: [],
                        balances: @json($balances),
                        paids: @json($paids),
                        collectors: @json($collectors),
                        collectorRole: '',
                        collectorYearLevel: '',
                        collectorBlock: '',

                        openModal(data) {
                        this.selectedId = data.id;
                        this.date_remitted = data.date_remitted;
                        this.collectedBy = data.collectedBy;
                        this.status = data.status;
                        this.selectedDateForRequest = this.formatDateForRequest(data.selectedDateForRequest);




                        this.studentName = data.collectedBy;
                        this.totalAmount = data.totalAmount;
                        this.payableCount = data.payableCount;

                        this.descriptions = Array.isArray(data.descriptions) ? data.descriptions : Object.values(data.descriptions);

                        this.showModal = true;
                        this.showPayableDetails = true;

                        const [firstname, lastname] = data.collectedBy.split(' ');
                        const collector = this.collectors.find(c =>
                            c.firstname === firstname && c.lastname === lastname
                        );

                        if (collector) {
                            this.collectorRole = collector.role;
                            this.collectorYearLevel = collector.yearLevel;
                            this.collectorBlock = collector.block;
                        } else {
                            this.collectorRole = 'N/A';
                            this.collectorYearLevel = 'N/A';
                            this.collectorBlock = 'N/A';
                        }
                    },


                        getBalance(desc) {
                            const match = this.balances.find(b =>
                                b.description === desc
                            );
                            return match ? parseFloat(match.balance).toFixed(2) : '0.00';
                        },

                            getPaid(desc) {
                                console.log("Selected Date from table:", this.selectedDateForRequest);
                                console.log("Selected Date for Request:", this.date_remitted);
                                console.log("Selected Collector for Request:", this.collectedBy);

                                const matchingPaids = this.paids.filter(b => {
                                    const matchesCommon = b.description === desc &&
                                        b.date_remitted === this.date_remitted &&
                                        b.status === this.status;

                                    if (this.status === 'TO TREASURER') {
                                        return matchesCommon && b.date_remitted === this.date_remitted;
                                    }

                                    return matchesCommon;
                                });

                            return matchingPaids
                                .reduce((total, paid) => total + parseFloat(paid.paid), 0)
                                .toFixed(2);
                        },


                        getTotalPaid() {
                        let totalPaid = 0;
                        if (Array.isArray(this.descriptions)) {
                            this.descriptions.forEach(desc => {
                                let paidAmount = parseFloat(this.getPaid(desc));
                                if (!isNaN(paidAmount)) {
                                    totalPaid += paidAmount;
                                }
                            });
                        } else {
                            console.error("Descriptions is not an array:", this.descriptions);
                        }
                        return totalPaid.toFixed(2);
                    },

                        fetchStudents(description) {
                            this.selectedDescription = description;
                            this.studentList = [];

                            fetch(`/treasurer/remitted/students?date=${this.selectedDateForRequest}&collectedBy=${this.studentName}&description=${encodeURIComponent(description)}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.length > 0) {
                                        this.studentList = data;
                                        this.showStudentListModal = true;
                                    } else {
                                        alert('No students found for this description.');
                                    }
                                })
                                .catch(err => {
                                    alert('Failed to fetch student list');
                                    console.error(err);
                                });
                        },

                        formatDateForRequest(date) {
                            const dateObj = new Date(date);
                            return `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
                        }
                    };
                }
            </script>





<style>
@keyframes checkmark {
0% { opacity: 0; transform: scale(0.5); }
100% { opacity: 1; transform: scale(1); }
}

.checkmark-animate {
animation: checkmark 0.3s ease-out forwards;
}
</style>

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
                        <td id="totalAmountCell" class="border border-black">₱0.00</td>
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
                document.getElementById('totalAmountCell').innerText = '₱' + total.toFixed(2);
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
                    <input type="date" id="selectedyear" name="date"
                        class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500 text-black" required>
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
                        <td id="totalAmountCell" class="border border-black">₱0.00</td>
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

<!-- Archive Confirmation Modal -->
<div id="archiveModalMale" class="fixed inset-0 flex items-center justify-center bg-white/60 z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96 h-[40%] border-2 border-[#1a4d2e] flex flex-col justify-center">
        <div class="flex flex-col items-center">
            <img class="w-[38%] h-[50%] mb-10">
            <p class="text-[#1a4d2e] text-center font-semibold">Are you sure you want to confirm this remittance? This action cannot be undone.</p>
            <div class="flex mt-10 space-x-4">
                <button type="button" class="cancelBtn bg-red-600 text-white px-6 py-2 rounded-lg shadow hover:bg-red-700 transition">CANCEL</button>
                <button type="button" class="confirmBtn bg-[#1a4d2e] text-white px-6 py-2 rounded-lg shadow-md hover:bg-green-700">CONFIRM</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModalMale" class="fixed inset-0 flex items-center justify-center bg-white/60 z-50 hidden">
    <div class="relative bg-white p-6 rounded-lg shadow-lg w-96 h-[40%] border-2 border-[#1a4d2e] flex flex-col justify-center">
        <div class="flex flex-col items-center">
            <img class="w-[38%] h-[50%] mb-10" >
            <p class="text-[#1a4d2e] text-center font-semibold">Remittance successfully confirmed and added to funds.</p>
            <div class="flex mt-10 space-x-4">
                <button type="submit" class="bg-[#1a4d2e] text-white px-6 py-2 rounded-lg shadow-md hover:bg-green-700">
                    CONTINUE
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('selectedyear');
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
        document.getElementById('totalAmountCell').innerText = '₱' + total.toFixed(2);
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
</div>


<script>
    var status = 'RECEIVED BY TREASURER';

    function handleRemitButtonClick() {
        var button = document.getElementById("remitButton");
        var selectedDate = document.getElementById("selectedyear").value;

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









</x-Add-components.sidebar>
</x-trea-components.content>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const archiveModal = document.getElementById("archiveModalMale");
    const successModal = document.getElementById("successModalMale");
    const confirmButton = archiveModal.querySelector(".confirmBtn");
    const cancelButton = archiveModal.querySelector(".cancelBtn");
    const Confirm = document.getElementById("confirm");
    const successConfirmButton = successModal.querySelector("button[type='submit']");



    confirmButton.addEventListener("click", function () {
        successModalMale.classList.add("hidden");
    });

    Confirm.addEventListener("click", function () {
        archiveModal.classList.remove("hidden");
    });



    confirmButton.addEventListener("click", function () {
        archiveModalMale.classList.add("hidden");
        successModalMale.classList.remove("hidden");
    });



    cancelButton.addEventListener("click", function () {
        archiveModal.classList.add("hidden");
    });

    successConfirmButton.addEventListener("click", function () {
        successModal.classList.add("hidden");
    });
});
</script>



<style>
@keyframes checkmark {
    0% { opacity: 0; transform: scale(0.5); }
    100% { opacity: 1; transform: scale(1); }
}

.checkmark-animate {
    animation: checkmark 0.3s ease-out forwards;
}
</style>


