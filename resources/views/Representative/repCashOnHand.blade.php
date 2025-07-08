<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>

    <x-Repre-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

        <div class="mt-4" x-data="remittanceComponent()">
            <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

            <x-trea-components.nav-link>
                <a href="/representative/collection" class="text-[17px] text-gray-600">Payment</a>
                <a href="/representative/remitted" class="text-[17px] text-gray-600">Remittance</a>
                <a href="/representative/CashOnHand" class="text-[17px] font-semibold text-green-700 border-b-2 border-green-700 pb-1">Cash on hand</a>
            </x-trea-components.nav-link>

            <div class="flex flex-col md:flex-row overflow-auto">
                <div class="w-full md:w-1/2 overflow-auto">
                    <div class=" overflow-auto sm:mr-4 md:mr-6 lg:mr-8 xl:mr-10">
                        <div class="flex flex-col md:flex-row md:justify-between items-start mb-4 w-full">
                            <x-trea-components.sorting class="w-full md:w-auto" />
                            <x-trea-components.year-sorting class="w-full md:w-auto" />
                        </div>

                        <div x-data="{
                            activeRow: null,
                            checkedRows: [],
                            selectedDates: [],
                            toggleRemittance(remittanceId, date) {
                                if (!this.checkedRows.includes(remittanceId)) {
                                    this.checkedRows.push(remittanceId);
                                    this.selectedDates.push(date);
                                } else {
                                    this.checkedRows = this.checkedRows.filter(id => id !== remittanceId);
                                    this.selectedDates = this.selectedDates.filter(d => d !== date);
                                }
                                console.log(this.selectedDates);
                            }
                         }">
                            <table class="w-full min-w-[600px] border border-black rounded-lg text-sm text-center">
                                <thead>
                                    <tr class="bg-green-700 text-white border border-black">
                                        <th class="p-2 border border-black"><input type="checkbox" id="selectAll"></th>
                                        <th class="p-2 border border-black">DATE</th>
                                        <th class="p-2 border border-black">COLLECTED BY</th>
                                        <th class="p-2 border border-black">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalAmount = 0;
                                        $groupedRemittances = $remittances->unique('id')->groupBy(function($remittance) {
                                            return \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') . '-' . $remittance->collectedBy;
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

                                        <tr :class="{'bg-gray-300': checkedRows.includes('{{ $remittance->id }}')}"
                                            class="border border-black hover:bg-gray-200 cursor-pointer"
                                            x-on:click="
                                                toggleRemittance(
                                                    '{{ $remittance->id }}',
                                                    '{{ \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') }}'
                                                );
                                                if (checkedRows.includes('{{ $remittance->id }}')) {
                                                    const remittanceData = {
                                                        id: '{{ $remittance->id }}',
                                                        date: '{{ \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') }}',
                                                        collectedBy: '{{ $remittance->collectedBy }}',
                                                        totalPaid: {{ $totalPaid }},
                                                        totalCollected: {{ $totalCollected }},
                                                        payableCount: {{ $payableCount }},
                                                        descriptions: @js($descriptions),
                                                        yearLevel: '{{ $remittance->yearLevel }}',
                                                        block: '{{ $remittance->block }}',
                                                        role: '{{ $remittance->role }}',

                                                    };
                                                    openModal(remittanceData, checkedRows);
                                                } else {
                                                    removeRemittance('{{ $remittance->id }}');
                                                }
                                            ">
                                            <td class="p-2 border border-black">
                                                <input type="checkbox" class="rowCheckbox"
                                                    x-bind:checked="checkedRows.includes('{{ $remittance->id }}')"
                                                    x-on:click.stop="event.stopPropagation()" />
                                            </td>
                                            <td class="p-2 border border-black">{{ \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') }}</td>
                                            <td class="p-2 border border-black">{{ $remittance->collectedBy }}</td>
                                            <td class="p-2 border border-black">{{ number_format($totalPaid + $totalCollected, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="p-2 border border-black font-bold" colspan="3">Total</td>
                                        <td class="p-2 border border-black font-bold">{{ number_format($totalAmount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="showModal"
                x-transition:enter="transition duration-300 transform"
                x-transition:enter-start="-translate-y-10 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition duration-200 transform"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="-translate-y-10 opacity-0"
                class="w-full md:w-1/2 p-4 mt-[4%] bg-gray-400 bg-opacity-40 shadow-md border-green-600 border-2 relative">
                <div class="flex flex-col md:flex-row items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <p id="studentName" class="text-[25px] font-bold text-green-700" x-text="studentName"></p>
                        <p class="text-[18px]"><span x-text="collectorYearLevel + ' - ' + collectorBlock"></span></p>


                            <div class="flex items-center space-x-2 mt-2">
                                <input type="date" id="selectedyear" name="date" class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500" required>
                            </div>
                    </div>

                    <div class="w-full md:w-auto overflow-x-auto flex md:text-center md:justify-end">
                        <table class="w-full border border-black shadow-lg rounded-lg">
                            <thead>
                                <tr class="bg-gray-800 text-white text-xs md:text-base">
                                    <th class="p-2 border border-black bg-green-700">CASH ON HAND</th>
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

                <!-- Payable Table -->
                <div x-show="showPayableDetails" class="mt-4">

                        <table class="w-full text-sm text-center border border-black">
                            <thead>
                                <tr class="bg-green-700 text-white">
                                    <th class="p-2 border border-black">Description</th>
                                    <th class="p-2 border border-black">Amount</th>
                                    <th class="p-2 border border-black">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>

                            <template x-for="desc in getAllUniqueDescriptions()" :key="desc">
                                <tr>
                                    <td class="border p-2 border-black font-bold" x-text="desc"></td>
                                    <td class="border p-2 border-black font-bold" x-text="getCombinedBalance(desc)"></td>
                                    <td class="border p-2 border-black  font-bold text-black" x-text="getCombinedPaid(desc)"></td>
                                </tr>
                            </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="p-2 border border-black font-bold" colspan="2">Total</td>
                                    <td class="p-2 border border-black font-bold" x-text="getTotalPaid()"></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mt-4 text-center">
                            <button id="remitButton" type="button"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-md transition duration-200">
                            REMIT
                        </button>
                        </div>



                </div>
            </div>


<x-confirm-denomitation/>




</x-Repre-components.sidebar>
</x-trea-components.content>

<script>
    function remittanceComponent() {
        return {
            selectedId: null,
            selectedDates: [],
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
            balances: @json($balances),
            paids: @json($paids),
            collectors: @json($collectors),
            collectorRole: '',
            collectorYearLevel: '',
            collectorBlock: '',
            collectorDate: '',
            selectedDescriptions: [],
            selectedRemittances: [],
            selectedCheckedIds: [],
            denominations: {},

            openModal(data) {
                this.selectedId = data.id;
                this.selectedDate = data.date;
                const formattedDate = this.formatDateForRequest(data.date);
                this.selectedDateForRequest = formattedDate;
                this.studentName = data.collectedBy;
                this.totalAmount = data.totalAmount;
                this.payableCount = data.payableCount;
                this.descriptions = data.descriptions;
                this.showModal = true;
                this.showPayableDetails = true;
                this.collectorRole = data.role;
                this.collectorYearLevel = data.yearLevel;
                this.collectorBlock = data.block;
                this.collectorDate = formattedDate;

                const alreadyExists = this.selectedRemittances.some(r => r.id === data.id);
                if (!alreadyExists) {
                    this.selectedRemittances.push({
                        id: data.id,
                        descriptions: data.descriptions,
                        collectedBy: data.collectedBy,
                        date: formattedDate,
                        totalPaid: data.totalPaid,
                        totalCollected: data.totalCollected,
                        yearLevel: data.yearLevel,
                        block: data.block,
                        role: data.role
                    });
                }

                if (!this.selectedDates.includes(formattedDate)) {
                    this.selectedDates.push(formattedDate);
                }

                document.getElementById('selectedDatesInput').value = this.selectedDates.join(',');

                this.selectedCheckedIds = this.selectedRemittances.map(r => r.id);
            },

            removeRemittance(id) {
                const removed = this.selectedRemittances.find(r => r.id === id);

                this.selectedRemittances = this.selectedRemittances.filter(remittance => remittance.id !== id);
                this.selectedCheckedIds = this.selectedCheckedIds.filter(checkedId => checkedId !== id);

                if (removed) {
                    const stillExists = this.selectedRemittances.some(r => r.date === removed.date);
                    if (!stillExists) {
                        this.selectedDates = this.selectedDates.filter(d => d !== removed.date);
                        document.getElementById('selectedDatesInput').value = this.selectedDates.join(',');
                    }
                }
            },

            formatDateForRequest(date) {
                const d = new Date(date);
                return d.toISOString().split('T')[0];
            },

            getAllUniqueDescriptions() {
                const allDescriptions = this.selectedRemittances.flatMap(r => r.descriptions);
                return [...new Set(allDescriptions)];
            },

            getCombinedBalance(desc) {
                const match = this.balances.find(b =>
                    b.description === desc &&
                    b.yearLevel === this.collectorYearLevel &&
                    b.block === this.collectorBlock
                );
                return match ? parseFloat(match.balance).toFixed(2) : '0.00';
            },

            getCombinedPaid(desc) {
                let total = 0;
                this.selectedRemittances.forEach(remit => {
                    const matched = this.paids.filter(p =>
                        p.description === desc &&
                        p.yearLevel === remit.yearLevel &&
                        p.block === remit.block &&
                        p.date === remit.date &&
                        p.status === (remit.role === 'REPRESENTATIVE' ? 'COLLECTED' : 'REMITTED')
                    );
                    total += matched.reduce((sum, p) => sum + parseFloat(p.paid), 0);
                });
                return total.toFixed(2);
            },

            getTotalPaid() {
                return this.getAllUniqueDescriptions().reduce((sum, desc) => {
                    return sum + parseFloat(this.getCombinedPaid(desc));
                }, 0).toFixed(2);
            }
        };
    }
    </script>


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









