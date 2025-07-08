<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>
<x-Repre-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

<div class="mt-4">
    <x-trea-components.content-header>STUDENT BALANCES</x-trea-components.content-header>


    <x-trea-components.table-dash>
      <h3 id="groupTitle" class="text-green-800 text-2xl font-bold">
      {{ session('yearLevel') }} 
    </h3>
    
        <p class="text-[15px] text-gray-700">Representative: <span id="representativeName" class="font-medium">{{ $firstname }} {{ $lastname }}</span></p>

        <input type="hidden" id="cashOnHandAmount" value="₱0.00">

        <div class="w-full overflow-x-auto mt-4">
            <table class="w-full md:w-auto border border-black shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs md:text-base">
                        <th class="p-3 border border-black bg-green-700">CASH ON HAND</th>
                        <th class="p-3 border border-black bg-blue-700">REMITTED</th>
                        <th class="p-3 border border-black bg-yellow-500">RECEIVABLE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white text-black text-center text-sm md:text-lg font-semibold">
                        <td class="p-3 border border-black" id="cashOnHandDisplay">₱0.00</td>
                        <td class="p-3 border border-black" id="remittedDisplay">₱0.00</td>
                        <td class="p-3 border border-black" id="receivableDisplay">₱0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-trea-components.table-dash>

    <div class="mt-4 overflow-auto">
        <x-scrollable-table height="max-h-[80%]">
            <thead>
                <tr class="bg-green-700 text-white border border-black text-center">
                    <th class="p-2 border border-black">ID NUMBER</th>
                    <th class="p-2 border border-black">LASTNAME</th>
                    <th class="p-2 border border-black">FIRSTNAME</th>
                    <th class="p-2 border border-black">YEAR AND BLOCK</th>
                    <th class="p-2 border border-black">BALANCE</th>
                </tr>
            </thead>
            <tbody id="usersTableBody" class="text-center">
                @php $grandTotal = 0; @endphp
                @forelse($students as $student)
                    @php
                        $totalBalance = $payables[$student->student_id]->total_balance ?? 0;
                        $grandTotal += $totalBalance;
                    @endphp
                    @if(strtoupper($student->yearLevel) == strtoupper(session('yearLevel')) && strtoupper($student->block) == strtoupper(session('block')))
                        <tr class="border border-black cursor-pointer student-row hover:bg-gray-200"
                            onclick="routeToStudentLedger('{{ $student->student_id }}')">
                            <td class="p-2 border border-black">{{ $student->student_id }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($student->lastname) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($student->firstname) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}</td>
                            <td class="p-2 border border-black balance-cell">₱{{ number_format($totalBalance, 2) }}</td>
                        </tr>
                    @endif
                @empty
                    <tr id="noStudentsRow">
                        <td colspan="5" class="p-2 border border-black text-red-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="font-bold text-center text-sm md:text-lg">
                    <td colspan="4" class="p-2 border border-black text-center text-white">TOTAL BALANCE:</td>
                    <td class="p-2 border border-black text-white">₱{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        </x-scrollable-table>
    </div>

    <script>
        function routeToStudentLedger(student_id) {
            window.location.href = "/representative/student-ledger/" + student_id;
        }

        const cashOnHand = @json($cashOnHand);
        const remitted = @json($remitted);
        const studentRows = document.querySelectorAll('.student-row');
        const totalBalanceCell = document.querySelector('tfoot td:last-child');

        let totalCash = 0;
        let totalRemitted = 0;
        let totalBalance = 0;

        studentRows.forEach(row => {
            const balance = parseFloat(row.querySelector('td:last-child').innerText.replace(/[₱,]/g, '')) || 0;
            totalBalance += balance;
        });

        totalBalanceCell.textContent = `₱${totalBalance.toFixed(2)}`;
        const receivableDisplay = document.getElementById('receivableDisplay');
        receivableDisplay.textContent = `₱${totalBalance.toFixed(2)}`;

        totalCash = parseFloat(cashOnHand[session('student_id')] ?? 0);
        totalRemitted = parseFloat(remitted[session('student_id')] ?? 0);

        document.getElementById('cashOnHandDisplay').textContent = `₱${totalCash.toFixed(2)}`;
        document.getElementById('remittedDisplay').textContent = `₱${totalRemitted.toFixed(2)}`;
    </script>
</div>

</x-trea-components.sidebar>
</x-trea-components.content>
