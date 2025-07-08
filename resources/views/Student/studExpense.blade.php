<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
<x-Student-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">

            <div class="mt-4">
            <x-trea-components.content-header>EXPENSES</x-trea-components.content-header>
                
            <x-trea-components.year-sorting/>

           <x-trea-components.sorting>
           </x-trea-components.sorting>
                
 
           <div x-data="{ showDetails: false, modalDate: '', modalSource: '', modalAmount: '' }" class="flex flex-col md:flex-row">
            <x-two-table-scrollable>
                <thead>
                    <tr class="bg-white border border-black">
                        <th class="p-2 border border-black bg-[#1a4d2e] text-white ">DATE</th>
                        <th class="p-2 border border-black bg-[#1a4d2e] text-white">FUND SOURCE</th>
                        <th class="p-2 border border-black bg-[#1a4d2e] text-white">AMOUNT SPENT</th>
                    </tr>
                </thead>
                <tbody x-data="{ selectedDate: null }">
                    @foreach($groupedExpenses as $date => $expensesForDate)
                        @php
                            $totalAmountForDate = 0;
                            $sources = [];
                        @endphp
            
                        @foreach($expensesForDate as $source => $expenses)
                            @php
                                $totalAmountForDate += $expenses->sum('amount');
                                $sources[] = $source;
                            @endphp
                        @endforeach
                        <tr 
                        x-bind:class="selectedDate === '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}' ? 'bg-gray-200' : ''"
                        class="cursor-pointer hover:bg-gray-200 transition-all duration-300 ease-in-out"
                        @click="selectedDate = '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}';
                                showDetails = true;
                                modalDate = '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}'; 
                                modalSource = '{{ implode(', ', $sources) }}'; 
                                modalAmount = '{{ $totalAmountForDate }}';
                                fetchExpenses('{{ $date }}', '{{ implode(', ', $sources) }}')">
                    
                        <td class="p-2 border border-black text-center">
                            {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                        </td>
                    
                        <td class="p-2 border border-black text-center">
                            {{ implode(', ', $sources) }} 
                        </td>
                        <td class="p-2 border border-black text-right">
                        ₱{{ number_format($totalAmountForDate, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="p-2 bg-[#1a4d2e] text-white border border-black text-center font-bold">TOTAL AMOUNT SPENT</td>
                        <td class="p-2 border border-black font-bold text-white text-right bg-[#1a4d2e]">
                        ₱{{ number_format($groupedExpenses->flatten()->sum('amount'),2) }}
                        </td>
                    </tr>
                </tfoot>
            </x-two-table-scrollable>
                     
          
        
            <div 
                x-show="showDetails"
                x-transition:enter="transition duration-300 transform"
                x-transition:enter-start="-translate-y-10 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition duration-200 transform"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="-translate-y-10 opacity-0"
                class="h-[100%] w-full md:w-1/2 mx-auto p-6 mt-4 bg-gray-300 bg-opacity-40 shadow-lg border-2 border-green-700 rounded-lg relative"
            >
                <div>
                    <input type="hidden" id="sourceDisplay">
        
                    <select id="sourceSelect" class="border border-black rounded-md px-3 py-1 text-sm focus:ring-green-700 w-64" onchange="updateSourceDisplay()">
                    </select>
                    
                    <div class="mt-2">
                        <input type="date" id="dateDisplay" name="date" class="border border-black rounded-md px-3 py-1 text-sm focus:ring-green-800 w-64" onchange="updateSourceDisplay()" readonly>
                    </div>
                    
        
                    <div class="mt-2">
                        <x-scrollable-table height="max-h-[35vh]">
                            <thead>
                                <tr class="bg-[#1a4d2e] text-white text-center">
                                    <th class="p-2 border border-black">DESCRIPTION</th>
                                    <th class="p-2 border border-black">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white text-center" id="payablesTableBody">
                            </tbody>
                            <tfoot>
                                <tr class="text-white font-bold bg-[#1a4d2e] text-center">
                                    <td class="p-2 border border-black">TOTAL</td>
                                    <td class="p-2 border border-black" id="totalAmountPaid">₱0.00</td>
                                </tr>
                            </tfoot>
                        </x-scrollable-table>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            let currentDate = '';  
        
            function resetSelections() {
                const selectElement = document.getElementById('sourceSelect');
                selectElement.innerHTML = '';
                document.getElementById('sourceDisplay').textContent = 'SOURCE';
        
                const defaultOption = document.createElement('option');
                defaultOption.value = '';  
                defaultOption.textContent = 'Select a source';
                selectElement.appendChild(defaultOption);
        
                const tbody = document.getElementById("payablesTableBody");
                tbody.innerHTML = "";
                document.getElementById("totalAmountPaid").textContent = '₱0.00';
            }
        
            function updateSourceDisplay() {
                const selectElement = document.getElementById('sourceSelect');
                const sourceDisplay = document.getElementById('sourceDisplay');
                const date = document.getElementById('dateDisplay').value;
        
                const selectedSource = selectElement.value;
        
                sourceDisplay.textContent = selectedSource;
                fetchExpenses(date, selectedSource);
            }
        
            function fetchExpenses(date, source) {
                if (!source || !date) return;
        
                if (date !== currentDate) {
                    resetSelections();
                    currentDate = date; 
                }

                if (!source) {
                    return;
                }
        
                fetch(`/student/get-expenses/${date}/${encodeURIComponent(source)}`)
                    .then(response => response.json())
                    .then(data => {
                        addSourcesToSelect(source);
        
                        document.getElementById("dateDisplay").value = date;
        
                        const tbody = document.getElementById("payablesTableBody");
                        tbody.innerHTML = "";
        
                        let total = 0;
        
                        const groupedByDescription = {};
        
                        data.forEach(expense => {
                            if (groupedByDescription[expense.description]) {
                                groupedByDescription[expense.description] += parseFloat(expense.amount);
                            } else {
                                groupedByDescription[expense.description] = parseFloat(expense.amount);
                            }
                        });
        
                        Object.keys(groupedByDescription).forEach(description => {
                            const amount = groupedByDescription[description];
                            total += amount;
        
                            const row = `
                                <tr>
                                    <td class="p-2 border border-black">${description}</td>
                                    <td class="p-2 border border-black">₱${amount.toFixed(2)}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
        
                        document.getElementById("totalAmountPaid").textContent = `₱${total.toFixed(2)}`;
                    })
                    .catch(error => {
                        console.error("Error fetching expenses:", error);
                    });
            }
        
            function addSourcesToSelect(sources) {
                const selectElement = document.getElementById('sourceSelect');
                
                const sourceArray = sources.split(',').map(source => source.trim());
            
                sourceArray.forEach(sourceText => {
                    if (![...selectElement.options].some(option => option.value === sourceText)) {
                        const option = document.createElement('option');
                        option.value = sourceText;
                        option.textContent = sourceText;
            
                        selectElement.appendChild(option);
                    }
                });
            }
        
            fetchExpenses('2025-04-01', 'Default Source'); 
        </script>
        

</div> 
     
  </div>
  





</x-Student-components.sidebar>

</x-trea-components.content>  

