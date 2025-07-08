<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>
<x-Add-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

<div class="mt-4" x-data="remittanceComponent()" x-init="initCheckboxListener()">
    <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

    <x-trea-components.nav-link>
        <a href="/admin/remitted" class="text-[17px] font-semibold text-green-700 border-b-2 border-green-700 pb-1">Remittance</a>
        <a href="/admin/CashOnHand" class="text-[17px] text-gray-600">Cash on hand</a>
    </x-trea-components.nav-link>

    <div class="flex flex-col md:flex-row overflow-auto">
        <div class="w-full md:w-1/2 overflow-auto">
            <div class="overflow-auto sm:mr-4 md:mr-6 lg:mr-8 xl:mr-10">
                <div class="flex flex-col md:flex-row md:justify-between items-start mb-4 w-full">
                    <x-trea-components.sorting class="w-full md:w-auto" />
                    <x-trea-components.year-sorting class="w-full md:w-auto" />
                </div>

  <table class="w-full min-w-[600px] border border-black rounded-lg text-sm text-center">
    <thead>
        <tr class="bg-[#1a4d2e] text-white border border-black">
            <th class="p-2 border border-black">
                <input type="checkbox" x-ref="checkAll" @change="toggleSelectAll()">
            </th>
            <th class="p-2 border border-black cursor-pointer" id="sortDate">DATE</th>
            <th class="p-2 border border-black cursor-pointer" id="sortCollectedBy">COLLECTED BY</th>
            <th class="p-2 border border-black">AMOUNT</th>
            <th class="p-2 border border-black cursor-pointer" id="sortStatus">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount = 0;
            $grouped = $remittances->unique('id')->groupBy(function($r) {
                return \Carbon\Carbon::parse($r->date)->format('Y-m-d') . '-' . $r->collectedBy . '-' . $r->status;
            });
        @endphp

        @foreach ($grouped as $groupKey => $rows)
            @php
                $r = $rows->first();
                $totalPaid = $rows->sum('paid');
                $totalCollected = $rows->sum('amountCollected');
                $descriptions = $rows->pluck('description')->unique();
                $totalAmount += $totalPaid + $totalCollected;
                $rowId = $r->id;
            @endphp
            <tr
                class="border hover:bg-gray-100 cursor-pointer"
                data-date="{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}"
                data-collectedby="{{ strtolower($r->collectedBy) }}"
                data-status="{{ strtolower($r->status) }}"
                data-total="{{ $totalPaid + $totalCollected }}"
                @click="() => {
                    const cb = $refs['cb_' + {{ $rowId }}];
                    cb.checked = !cb.checked;
                    cb.dispatchEvent(new Event('change'));
                }"
            >
                <td class="p-2 border border-black">
                    <input type="checkbox"
                        x-ref="cb_{{ $rowId }}"
                        :value="{{ $rowId }}"
                        x-model="selected"
                        @change="collectRowData($event)"
                        @click.stop
                        :class="{
                            'accent-green-600': '{{ strtoupper($r->status) }}' === 'REMITTED',
                            'accent-red-600': '{{ strtoupper($r->status) }}' === 'TO TREASURER',
                            'accent-blue-600': !['REMITTED', 'TO TREASURER', 'COLLECTED', 'COLLECTED BY TREASURER'].includes('{{ strtoupper($r->status) }}')
                        }"
                        data-id="{{ $rowId }}"
                        data-date="{{ \Carbon\Carbon::parse($r->date)->format('F d, Y') }}"
                        data-dateforcompare="{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}"
                        data-collectedby="{{ $r->collectedBy }}"
                        data-status="{{ $r->status }}"
                        data-totalpaid="{{ $totalPaid }}"
                        data-totalcollected="{{ $totalCollected }}"
                        data-descriptions='@json($descriptions)'
                    />
                </td>
                <td class="p-2 border border-black">{{ \Carbon\Carbon::parse($r->date)->format('F d, Y') }}</td>
                <td class="p-2 border border-black">{{ $r->collectedBy }}</td>
                <td class="p-2 border border-black">{{ number_format($totalPaid + $totalCollected, 2) }}</td>
                <td class="p-2 border border-black font-bold">{{ strtoupper($r->status) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="p-2 bg-[#1a4d2e] text-white font-bold">Total</td>
            <td colspan="3" class="p-2 font-bold" id="totalAmountCell">{{ number_format($totalAmount, 2) }}</td>
        </tr>
    </tfoot>
</table>
</div>
</div>

        <!-- PAYABLE DETAILS MODAL -->
        <div x-show="showPayableDetails" class="mt-[73px] w-full md:w-1/2 pl-4">
           <div>
        <table class="w-full border text-center">
            <thead class="bg-[#1a4d2e] text-white">
                <tr>
                    <th class="p-2 border">Description</th>
                    <th class="p-2 border">Amount</th>
                    <th class="p-2 border">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="desc in descriptions" :key="desc">
                    <tr class="bg-white hover:bg-gray-100 cursor-pointer" @click="fetchStudents(desc)">
                        <td class="p-2 border" x-text="desc"></td>
                        <td class="p-2 border" x-text="getBalance(desc)"></td>
                        <td class="p-2 border" x-text="getPaid(desc)"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="p-2 bg-[#1a4d2e] text-white font-bold">Total</td>
                    <td class="p-2 border font-bold" x-text="getTotalPaid()"></td>
                </tr>
            </tfoot>
        </table>

    <!-- Approve All Button -->
<div class="mt-4 text-left" 
     x-show="!['REMITTED', 'COLLECTED', 'TO TREASURER', 'COLLECTED BY TREASURER'].includes(status.toUpperCase())">
    <!-- Approve All Button -->
<button
    class="px-4 py-2 bg-green-700 text-white font-semibold rounded hover:bg-green-800"
    @click="triggerApproval()"
>
    Approve All
</button>

<!-- ✅ Confirmation Modal with Alpine.js -->
<div x-show="showConfirmationModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
  <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-green-700 transition-all duration-300">
    <div class="flex flex-col items-center space-y-6">
      <!-- Icon -->
      <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z"/>
      </svg>

      <!-- Message -->
      <p class="text-lg text-center text-gray-800 font-semibold">
        Are you sure you want to approve the selected remittances?
      </p>

      <!-- Buttons -->
      <div class="flex justify-center space-x-4 mt-2">
        <button type="button"
                @click="showConfirmationModal = false"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
          Cancel
        </button>

  <button
    type="button"
    @click="isProcessing = true; confirmApproveAll()"
    :disabled="isProcessing"
    x-text="isProcessing ? 'Processing...' : 'Confirm'"
    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-md font-medium transition min-w-[120px] text-center"
>
    Confirm
</button>


      </div>
    </div>
  </div>
</div>

    <button
        class="px-4 py-2 bg-red-700 text-white font-semibold rounded hover:bg-red-800"
        @click="rejectAll()"
    >
        Reject All
    </button>
</div>
</div>
</div>
</div>

    <!-- STUDENT LIST MODAL -->
    <div x-show="showStudentListModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2" @click.stop>
            <h2 class="text-xl font-bold mb-4">Payment Record: <span x-text="selectedDescription"></span></h2>
            <p class="mb-2"><strong>Date:</strong> <span x-text="selectedDate"></span></p>
            <table class="w-full border text-center">
                <thead class="bg-[#1a4d2e] text-white">
                    <tr>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Description</th>
                        <th class="p-2 border">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="student in studentList" :key="student.id + '-' + student.description + '-' + student.paid">
                        <tr>
                            <td class="p-2 border" x-text="student.firstname + ' ' + student.lastname"></td>
                            <td class="p-2 border" x-text="selectedDescription"></td>
                            <td class="p-2 border" x-text="parseFloat(student.paid).toFixed(2)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="flex justify-center mt-4">
                <button @click="showStudentListModal = false" class="px-4 py-2 bg-[#1a4d2e] text-white rounded hover:bg-green-700">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function remittanceComponent() {
    return {
        isProcessing: false,
        selected: [],
        selectedRowsData: [],
        showModal: false,
        showPayableDetails: false,
        showStudentListModal: false,
        selectedDescription: '',
        selectedDate: '',
        selectedDateForRequest: '',
        collectedBy: '',
        status: '',
        descriptions: [],
        studentList: [],
        collectorRole: '',
        collectorYearLevel: '',
        collectorBlock: '',
        balances: @json($balances),
        paids: @json($paids),
        collectors: @json($collectors),

        initCheckboxListener() {
            this.$nextTick(() => {
                if (this.$refs.checkAll) {
                    this.$refs.checkAll.indeterminate = false;
                }
            });
        },

  toggleSelectAll() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    const allRows = document.querySelectorAll("tbody tr");
    const visibleRows = Array.from(allRows).filter(row => row.offsetParent !== null);

    const isFilteredOrSorted = visibleRows.length < allRows.length;

    const visibleStatuses = visibleRows.map(row => {
        const cb = row.querySelector('input[type="checkbox"]');
        return cb?.dataset.status?.toUpperCase() || '';
    }).filter(Boolean);

    const uniqueStatuses = [...new Set(visibleStatuses)];
    if (isFilteredOrSorted && uniqueStatuses.length > 1) {
        return;
    }

    const excludedStatuses = ['REMITTED', 'COLLECTED', 'TO TREASURER', 'COLLECTED BY TREASURER'];

    const eligibleCheckboxes = Array.from(checkboxes).filter(cb => {
        const row = cb.closest('tr');
        const isVisible = row && row.offsetParent !== null;
        const status = cb.dataset.status?.toUpperCase();
        return isVisible && (!excludedStatuses.includes(status) || isFilteredOrSorted);
    });

    const eligibleIds = eligibleCheckboxes.map(cb => parseInt(cb.value));
    const isMasterChecked = this.$refs.checkAll.checked;

    this.selected = isMasterChecked ? eligibleIds : [];
    this.selectedRowsData = [];

    eligibleCheckboxes.forEach(cb => {
        cb.checked = isMasterChecked;
        if (cb.checked) {
            this.collectRowData({ target: cb });
        }
    });

    // Optional: Uncheck other non-eligible checkboxes
    checkboxes.forEach(cb => {
        if (!eligibleCheckboxes.includes(cb)) {
            cb.checked = false;
        }
    });

    this.showModal = this.selectedRowsData.length > 0;

    // Don't auto-check header from this point on
    if (this.$refs.checkAll) {
        this.$refs.checkAll.indeterminate = false;
    }
},
   collectRowData(event) {
    const cb = event.target;
    const id = parseInt(cb.dataset.id);
    const status = cb.dataset.status.toUpperCase();

    if (cb.checked) {
        if (!this.selected.includes(id)) {
            this.selected.push(id);
        }

        const exists = this.selectedRowsData.find(row => row.id === id);
        if (!exists) {
            this.selectedRowsData.push({
                id,
                date: cb.dataset.date,
                dateForCompare: cb.dataset.dateforcompare,
                collectedBy: cb.dataset.collectedby,
                status: cb.dataset.status,
                totalPaid: parseFloat(cb.dataset.totalpaid),
                totalCollected: parseFloat(cb.dataset.totalcollected),
                descriptions: JSON.parse(cb.dataset.descriptions)
            });
        }
    } else {
        this.selected = this.selected.filter(i => i !== id);
        this.selectedRowsData = this.selectedRowsData.filter(row => row.id !== id);
    }

    const statuses = [...new Set(this.selectedRowsData.map(row => row.status.toUpperCase()))];
    const isMixed = statuses.length > 1;

    const excludedStatuses = ['REMITTED', 'COLLECTED', 'TO TREASURER', 'COLLECTED BY TREASURER'];
    if (isMixed) {
        this.selectedRowsData = this.selectedRowsData.filter(row => !excludedStatuses.includes(row.status.toUpperCase()));
        this.selected = this.selectedRowsData.map(row => row.id);
    }

    this.toggleRemittedCheckboxes(isMixed);
    this.showModal = this.selectedRowsData.length > 0;
    if (this.showModal) this.updateModalData();
    },

    toggleRemittedCheckboxes(isMixed) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-status]');
        const excludedStatuses = ['REMITTED', 'COLLECTED', 'TO TREASURER', 'COLLECTED BY TREASURER'];

        checkboxes.forEach(cb => {
            const status = cb.dataset.status.toUpperCase();
            if (excludedStatuses.includes(status)) {
                cb.disabled = isMixed;
                if (isMixed) cb.checked = false;
            } else {
                cb.disabled = false;
            }
        });
    },



        updateModalData() {
            const allDescriptions = new Set();
            const allDates = [];

            this.selectedRowsData.forEach(row => {
                row.descriptions.forEach(desc => allDescriptions.add(desc));
                allDates.push(row.dateForCompare);
            });

            const uniqueDates = [...new Set(allDates)].sort();
            const first = this.selectedRowsData[0];

            if (uniqueDates.length === 1) {
                this.selectedDate = first.date;
            } else {
                const start = new Date(uniqueDates[0]);
                const end = new Date(uniqueDates[uniqueDates.length - 1]);
                const opts = { month: 'long', day: 'numeric' };
                const startText = start.toLocaleDateString('en-US', opts);
                const endText = end.toLocaleDateString('en-US', opts);
                const year = end.getFullYear();
                this.selectedDate = `${startText} - ${endText}, ${year}`;
            }

            this.collectedBy = first.collectedBy;
            this.status = first.status;
            this.selectedDateForRequest = first.dateForCompare;
            this.descriptions = Array.from(allDescriptions);

            const [fname, lname] = this.collectedBy.split(' ');
            const match = this.collectors.find(c => c.firstname === fname && c.lastname === lname);
            this.collectorRole = match ? match.role : 'N/A';
            this.collectorYearLevel = match ? match.yearLevel : 'N/A';
            this.collectorBlock = match ? match.block : 'N/A';

            this.showPayableDetails = true;
        },
           triggerApproval() {
    if (!this.selectedRowsData || this.selectedRowsData.length === 0) {
        alert("No remittances selected.");
        return;
    }

    this.showConfirmationModal = true;
    },

    confirmApproveAll() {
    const dates = [...new Set(this.selectedRowsData.map(row => row.dateForCompare))];
    const confirmBtn = document.querySelector('[x-data] button.bg-gray-700');

    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';
    }

    fetch("{{ route('admin.remittance.approve') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').getAttribute("content")
        },
        body: JSON.stringify({ dates })
    })
    .then(response => {
        if (!response.ok) throw new Error("Approval failed");
        return response.json();
    })
    .then(() => {
        this.showConfirmationModal = false;
        window.location.reload();
    })
    .catch(error => {
        console.error(error);
        alert("Something went wrong.");
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm';
        }
    });
    },


        getPaid(desc) {
            const total = this.paids.filter(p =>
                this.selectedRowsData.some(row =>
                    row.dateForCompare === p.date &&
                    row.collectedBy === p.collectedBy &&
                    row.status === p.status &&
                    p.description === desc
                )
            ).reduce((sum, p) => sum + parseFloat(p.paid || 0), 0);
            return total.toFixed(2);
        },

        getBalance(desc) {
            const entry = this.balances.find(b => b.description === desc);
            return entry ? parseFloat(entry.balance).toFixed(2) : '0.00';
        },

        getTotalPaid() {
            return this.descriptions.reduce((sum, d) => sum + parseFloat(this.getPaid(d)), 0).toFixed(2);
        },

        fetchStudents(desc) {
            this.selectedDescription = desc;
            this.studentList = [];

            const promises = this.selectedRowsData
                .filter(row => row.descriptions.includes(desc))
                .map(row => {
                    const url = `/admin/remitted/students?status=${row.status}&date=${row.dateForCompare}&collectedBy=${encodeURIComponent(row.collectedBy)}&description=${encodeURIComponent(desc)}`;
                    return fetch(url)
                        .then(res => res.ok ? res.json() : [])
                        .catch(err => {
                            console.error(`Failed to fetch from ${url}`, err);
                            return [];
                        });
                });

            Promise.all(promises)
                .then(results => {
                    const combined = results.flat();

                    const merged = {};

                    for (const s of combined) {
                        const key = `${s.firstname} ${s.lastname}-${s.description}`;
                        if (!merged[key]) {
                            merged[key] = {
                                id: s.id,
                                firstname: s.firstname,
                                lastname: s.lastname,
                                description: s.description,
                                paid: parseFloat(s.paid)
                            };
                        } else {
                            merged[key].paid += parseFloat(s.paid);
                        }
                    }

                    this.studentList = [];
                    this.$nextTick(() => {
                        this.studentList = Object.values(merged);
                        this.showStudentListModal = true;
                    });
                });
        }
    };
    }   
</script>





</x-Add-components.sidebar>
</x-trea-components.content>
