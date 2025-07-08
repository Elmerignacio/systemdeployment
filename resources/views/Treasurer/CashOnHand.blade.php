<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>

<x-trea-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4" x-data="remittanceComponent()">
            <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

            <x-trea-components.nav-link>
                <a href="/treasurer/collection" class="text-[17px] text-gray-600">Payment</a>
                <a href="/treasurer/remitted" class="text-[17px] text-gray-600">Remittance</a>
                <a href="/treasurer/CashOnHand" class="text-[17px] font-semibold text-[#1a4d2e] border-b-2 border-[#1a4d2e] pb-1">Cash on hand</a>
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
                                   $groupedRemittances = $remittances->groupBy(function($remittance) {
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
                    
                        <tr :class="{'bg-gray-300': activeRow === '{{ $remittance->student_id }}'}" class="border border-black hover:bg-gray-200 cursor-pointer"
                            @click="activeRow = (activeRow === '{{ $remittance->student_id }}') ? null : '{{ $remittance->student_id }}'; openModal({
                                student_id: '{{ $remittance->student_id }}',
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
                     <div class="mt-4 text-center">
                        <button
                        id="remitButton"
                        type="button"
                        class="bg-[#1a4d2e] hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-md transition duration-200"
                        x-text="status === 'TO TREASURER' ? 'CONFIRM' : 'REMIT'"
                        onclick="handleRemitButtonClick()">
                    </button>
                        </div>
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
            

            
  <x-trea-components.denomanation-form/>

        



<!-- Archive Confirmation Modal -->
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const confirmpayment = document.getElementById("confirmpayment");
    const successpayment = document.getElementById("successpayment");
    const confirmButton = confirmpayment?.querySelector(".confirmBtn");
    const cancelButton = confirmpayment?.querySelector(".cancelBtn");
    const confirmTrigger = document.getElementById("confirm"); 
    const successConfirmButton = successpayment?.querySelector("button[type='submit']");

    if (confirmTrigger) {
        confirmTrigger.addEventListener("click", function () {
            confirmpayment?.classList.remove("hidden");
        });
    }

    if (confirmButton) {
        confirmButton.addEventListener("click", function () {
            confirmpayment?.classList.add("hidden");
            successpayment?.classList.remove("hidden");
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener("click", function () {
            confirmpayment?.classList.add("hidden");
        });
    }

    if (successConfirmButton) {
        successConfirmButton.addEventListener("click", function () {
            successpayment?.classList.add("hidden");
        });
    }
});
</script>







  
</x-trea-components.sidebar>
</x-trea-components.content>




	