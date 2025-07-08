<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>

<x-trea-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4" x-data="remittanceComponent()">
            <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

            <x-trea-components.nav-link>
                <a href="/treasurer/collection" class="text-[17px] text-gray-600">Payment</a>
                <a href="/treasurer/remitted" class="text-[17px] font-semibold text-green-700 border-b-2 border-green-700 pb-1">Remittance</a>
                <a href="/treasurer/CashOnHand" class="text-[17px] text-gray-600">Cash on hand</a>
            </x-trea-components.nav-link>

            <div class="flex flex-col md:flex-row overflow-auto">
                <div class="w-full md:w-1/2 overflow-auto">
                    <div class=" overflow-auto sm:mr-4 md:mr-6 lg:mr-8 xl:mr-10">
                        <div class="flex flex-col md:flex-row md:justify-between items-start mb-4 w-full">
                            <x-trea-components.sorting class="w-full md:w-auto" />
                            <x-trea-components.year-sorting class="w-full md:w-auto" />
                        </div>

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
                                        return \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') . '-' . $remittance->collectedBy . '-' . $remittance->status;
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
                                        @click="activeRow = (activeRow === '{{ $remittance->id }}') ? null : '{{ $remittance->id }}';
                                         openModal({
                                            id: '{{ $remittance->id }}',
                                            date: '{{ \Carbon\Carbon::parse($remittance->date)->format('F d, Y') }}',
                                            collectedBy: '{{ $remittance->collectedBy }}',
                                            totalPaid: {{ $totalPaid }},
                                            totalCollected: {{ $totalCollected }}, 
                                            payableCount: {{ $payableCount }},
                                            descriptions: @js($descriptions),
                                            status: '{{ $remittance->status }}' 
                                        })">
                                        <td class="p-2 border border-black">{{ \Carbon\Carbon::parse($remittance->date)->format('F d, Y') }}</td>
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
                                    <td class="p-2 -border bg-[#1a4d2e] border-black font-bold text-lg text-white " colspan="2" >Total</td>
                                    <td class="p-2 border border-black font-bold text-lg"  x-text="totalAmount"></td>
                                </tr>
                            </tfoot>
                        </table>
                        
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
                    <p id="collectedBy" class="text-[25px] font-bold text-[#1a4d2e]" x-text="collectedBy"></p>
                    <p class="text-[18px]"><span x-text="collectorYearLevel + ' - ' + collectorBlock"></span></p>
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
                                <th class="p-2 border border-black">Description</th>
                                <th class="p-2 border border-black">Amount</th>
                                <th class="p-2 border border-black">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="desc in descriptions" :key="desc">
                                <tr class="cursor-pointer bg-white text-black" @click="fetchStudents(desc)">
                                    <td class="p-2 border border-black" x-text="desc"></td>
                                    <td class="p-2 border border-black" x-text="getBalance(desc)"></td>
                                    <td class="p-2 border border-black" x-text="getPaid(desc)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="p-2 border bg-[#1a4d2e] text-white border-black font-bold" colspan="2">Total</td>
                                <td class="p-2 border bg-white  border-black font-bold" x-text="getTotalPaid()"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        

<style>
@keyframes checkmark {
0% { opacity: 0; transform: scale(0.5); }
100% { opacity: 1; transform: scale(1); }
}

.checkmark-animate {
animation: checkmark 0.3s ease-out forwards;
}
</style>

            <div x-show="showStudentListModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/2" @click.stop>
                    <h2 class="text-xl font-bold mb-4">Payment Record:  <span x-text="selectedDescription"></span></h2>
                    <p class="mb-2"><strong>Date:</strong> <span x-text="selectedDate"></span></p>

                    <table class="w-full text-sm text-center border border-black">
                        <thead>
                            <tr class="bg-[#1a4d2e] text-white">
                                <th class="p-2 border border-black">Name</th>
                                <th class="p-2 border border-black">Description</th>
                                <th class="p-2 border border-black">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(student, index) in studentList" :key="student.id || index">
                                <tr>
                                    <td class="p-2 border border-black" x-text="student.firstname + ' ' + student.lastname"></td>
                                    <td class="p-2 border border-black" x-text="selectedDescription"></td>
                                    <td class="p-2 border border-black" x-text="parseFloat(student.paid).toFixed(2)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                     <div class="flex justify-center">
                    <button @click="showStudentListModal = false" class="mt-4 px-4 py-2 bg-[#1a4d2e] text-white rounded-md hover:bg-green-700">Close</button>
                </div>
            </div>
        </div>

    
</x-trea-components.sidebar>
</x-trea-components.content>

<script>
    function remittanceComponent() {
        return {    
            selectedId: null,
            selectedDate: '',
            selectedDateForRequest: '',
            totalAmount: 0,
            payableCount: 0,
            showModal: false,
            showPayableDetails: false,
            showStudentListModal: false,
            selectedDescription: '',
            studentList: [],
            descriptions: [],
            status: [],
            collectedBy:[],
            balances: @json($balances),
            paids: @json($paids),
            collectors: @json($collectors), 
            collectorRole: '',
            collectorYearLevel: '',
            collectorBlock: '',
            collectorDate: '',
            
            openModal(data) {
                this.selectedId = data.id;
                this.selectedDate = data.date;
                this.selectedDateForRequest = this.formatDateForRequest(data.date);
                this.totalAmount = data.totalAmount;
                this.payableCount = data.payableCount;
                this.descriptions = Array.isArray(data.descriptions) ? data.descriptions : Object.values(data.descriptions); // Convert to array if it's an object
                console.log("Descriptions:", this.descriptions); 
                 this.status = data.status;
                this.collectedBy = data.collectedBy;
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
            console.log('Collected By:', this.collectedBy);

            const [firstname, lastname] = this.collectedBy.split(' ');
            const matchingPaids = this.paids.filter(b => {
                return b.description === desc &&
                    b.date === this.selectedDateForRequest &&
                    b.collectedBy === this.collectedBy && 
                    b.status === this.status;
            });
            console.log('Matching Paids:', matchingPaids); 

            return matchingPaids.reduce((total, entry) => total + parseFloat(entry.paid || 0), 0);
        },
        getTotalPaid() {
            let totalPaid = 0;

    if (Array.isArray(this.descriptions)) {
        this.descriptions.forEach(desc => {
            let paidAmount = this.getPaid(desc); 
            if (!isNaN(paidAmount)) {
                totalPaid += paidAmount;
            }
        });
    } else {
        console.error('Descriptions is not an array:', this.descriptions);
    }

    return totalPaid.toFixed(2); 
},


        fetchStudents(description) {
        this.selectedDescription = description;
        this.studentList = []; 

        fetch(`/treasurer/remitted/students?status=${this.status}&date=${this.selectedDateForRequest}&collectedBy=${this.collectedBy}&description=${encodeURIComponent(description)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    this.studentList = data;
                    this.showStudentListModal = true;
                } else {
                    alert('No students found for this description.');
                    this.studentList = [];
                }
            })
            .catch(err => {
                alert('Failed to fetch student list');
                console.error(err);
                this.studentList = [];
            });
    },


            formatDateForRequest(date) {
                const dateObj = new Date(date);
                return `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
            }
        };
    }
</script>