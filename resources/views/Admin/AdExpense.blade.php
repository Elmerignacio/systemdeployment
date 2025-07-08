<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
<x-Add-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">

            <div class="mt-4">
            <x-trea-components.content-header>EXPENSES</x-trea-components.content-header>
                
            <x-trea-components.year-sorting/>

           <x-trea-components.sorting>
            <a href="#" onclick="openModal()" class="bg-[#1a4d2e] text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-800">
              Add Expense <i class="fas fa-plus"></i>
            </a>
           </x-trea-components.sorting>
                
 
           <div x-data="{ showDetails: false, modalDate: '', modalSource: '', modalAmount: '' }" class="flex flex-col md:flex-row">
            <x-two-table-scrollable>
                <thead>
                    <tr class="bg-white border border-black">
                        <th class="p-2 border border-black text-center text-white bg-[#1a4d2e]">DATE</th>
                        <th class="p-2 border border-black text-center text-white bg-[#1a4d2e]">FUND SOURCE</th>
                        <th class="p-2 border border-black text-center text-white bg-[#1a4d2e]">AMOUNT SPENT</th>
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
                        <td class="p-2 border border-black text-right">₱
                            {{ number_format($totalAmountForDate, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="p-2 bg-[#1a4d2e] text-white border border-black text-center font-bold">TOTAL AMOUNT SPENT</td>
                        <td class="p-2 border border-black font-bold bg-[#1a4d2e] text-right text-white">₱
                            {{ number_format($groupedExpenses->flatten()->sum('amount'),2) }}
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
                                    <td class="p-2 border border-black text-right" id="totalAmountPaid">₱0.00</td>
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
        
                fetch(`/admin/get-expenses/${date}/${encodeURIComponent(source)}`)
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
                                    <td class="p-2 text-right border border-black">₱${amount.toFixed(2)}</td>
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
     

   


    














      
     <div id="expenseModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
      <div class="bg-green-900 rounded-xl w-[900px] p-6 relative text-white shadow-2xl">
          <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-red-400 font-bold">
              &times;
          </button>
          
          <h2 class="text-2xl font-bold text-center mb-6">CREATE EXPENSES</h2>
  
          <form method="POST" action="{{ route('Adexpenses.store') }}">
              @csrf
              <div class="flex justify-between items-center">
                  <div class="flex-1 mb-3">
                      <select id="descriptionSelect" name="description" class="border w-[40%] h-9 border-black rounded-md px-3 py-1 text-black" required>
                          <option value="" disabled selected>Select Description</option>
                          @foreach($descriptions as $desc)
                              <option value="{{ $desc }}">{{ $desc }}</option>
                          @endforeach
                      </select>
                      <div class="flex items-center space-x-2 mt-2 text-black">
                          <input type="date" id="selectedyear" name="date" class="w-[40%] h-9 border border-black rounded-md px-3 py-1 text-sm" required>
                      </div>
                  </div>
  
                  <div class="flex justify-end">
                      <div class="text-lg text-left">
                          <p class="font-bold text-lg" id="availableBalance">Available Balance: ₱0.00</p>
                          <p class="font-bold" id="totalExpenses">Total Expenses: ₱0.00</p>
                      </div>
                  </div>
              </div>
  
              <div class="relative border-white">
                  <table id="table" class="w-full min-w-[600px] border border-black rounded-lg text-sm text-center">
                      <thead>
                          <tr class="bg-white text-black border border-black">
                              <th class="p-2 border border-black bg-white">DESCRIPTION</th>
                              <th class="p-2 border border-black bg-white">QTY</th>
                              <th class="p-2 border border-black bg-white">LABEL</th>
                              <th class="p-2 border border-black bg-white">PRICE</th>
                              <th class="p-2 border border-black bg-white">AMOUNT</th>
                          </tr>
                      </thead>
                      <tbody id="tableBody">
                          <tr class="bg-white text-black">
                              <td class="p-2 border border-black"><input type="text" name="items[0][description]" class="w-full p-1" required /></td>
                              <td class="p-2 border border-black"><input type="number" name="items[0][quantity]" class="w-full p-1 qty" required /></td>
                              <td class="p-2 border border-black"><input type="text" name="items[0][label]" class="w-full p-1" required /></td>
                              <td class="p-2 border border-black"><input type="number" step="0.01" name="items[0][price]" class="w-full p-1 price" required /></td>
                              <td class="p-2 border border-black"><input type="number" step="0.01" name="items[0][amount]" class="w-full p-1 amount" readonly /></td>
                          </tr>
                      </tbody>
                  </table>
  
                  <div class="absolute left-[100%] translate-x-[-60%] bottom-[-14px] flex flex-col">
                      <button type="button" id="addRowButton" class="bg-green-600 hover:bg-green-700 w-6 h-6 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg border-green-600">+</button>
                  </div>
              </div>
  
              <div class="mt-4 flex justify-center">
                  <button id="disburseButton" type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow border border-green-600">DISBURSE</button>
              </div>
          </form>
  
          <script>
              const paidData = @json($paidData);
              let rowIndex = 1;
              let originalBalance = 0;
  
              document.getElementById('descriptionSelect').addEventListener('change', function () {
                  const selected = this.value;
                  const paid = paidData[selected] || 0;
                  originalBalance = paid;
                  updateAmounts(); 
              });
  
              function updateAmounts() {
                  let total = 0;
                  document.querySelectorAll('#table tbody tr').forEach(row => {
                      const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
                      const price = parseFloat(row.querySelector('.price')?.value) || 0;
                      const amount = qty * price;
                      row.querySelector('.amount').value = amount.toFixed(2);
                      total += amount;
                  });
  
                  const remainingBalance = originalBalance - total;
  
                  document.getElementById('totalExpenses').textContent = 'Total Expenses: ₱' + total.toFixed(2);
                  document.getElementById('availableBalance').textContent = 'Available Balance: ₱' + remainingBalance.toLocaleString(undefined, { minimumFractionDigits: 2 });
  
                  const disburseBtn = document.getElementById('disburseButton');
                  if (remainingBalance < 0) {
                      disburseBtn.disabled = true;
                      disburseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                  } else {
                      disburseBtn.disabled = false;
                      disburseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                  }
              }
  
              document.getElementById('tableBody').addEventListener('input', function (e) {
                  if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
                      updateAmounts();
                  }
              });
  
              document.getElementById('addRowButton').addEventListener('click', () => {
                  const tbody = document.getElementById('tableBody');
                  const newRow = document.createElement('tr');
                  newRow.classList.add('bg-white', 'text-black');
  
                  newRow.innerHTML = `
                      <td class="p-2 border border-black"><input type="text" name="items[${rowIndex}][description]" class="w-full p-1" required /></td>
                      <td class="p-2 border border-black"><input type="number" name="items[${rowIndex}][quantity]" class="w-full p-1 qty" required /></td>
                      <td class="p-2 border border-black"><input type="text" name="items[${rowIndex}][label]" class="w-full p-1" required /></td>
                      <td class="p-2 border border-black"><input type="number" step="0.01" name="items[${rowIndex}][price]" class="w-full p-1 price" required /></td>
                      <td class="p-2 border border-black"><input type="number" step="0.01" name="items[${rowIndex}][amount]" class="w-full p-1 amount" readonly /></td>
                  `;
                  rowIndex++;
                  tbody.appendChild(newRow);
                  updateAmounts(); 
              });
  
              function openModal() {
                  document.getElementById("expenseModal").classList.remove("hidden");
              }
  
              function closeModal() {
                  document.getElementById("expenseModal").classList.add("hidden");
              }
  
              window.onclick = function(event) {
                  const modal = document.getElementById("expenseModal");
                  if (event.target === modal) {
                      closeModal();
                  }
              }
          </script>
      </div>
  </div>
  





</x-Add-components.sidebar>

</x-trea-components.content>  

