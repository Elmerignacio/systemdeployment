<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>
<x-trea-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

<div class="mt-4">
    <x-trea-components.content-header>STUDENT BALANCES</x-trea-components.content-header>

    <x-trea-components.table-guide>
        <select id="yearLevelFilter" class="border mt-3 border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
            <option value="" selected>All YEAR LEVEL</option>
            @foreach ($yearLevels as $yearLevel)
                <option value="{{ $yearLevel->yearLevel }}">{{ $yearLevel->yearLevel }}</option>
            @endforeach
        </select>

        <select id="blockFilter" class="border mt-3 border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
            <option value="" selected>All BLOCK</option>
            @foreach ($blocks as $block)
                <option value="{{ $block->block }}">{{ $block->block }}</option>
            @endforeach
        </select>
    </x-trea-components.table-guide>

    <x-trea-components.table-dash>
        <h3 id="groupTitle" class="text-green-800 text-2xl font-bold">ALL YEAR LEVEL</h3>
        <p class="text-[15px] text-gray-700">Representative: <span id="representativeName" class="font-medium"></span></p>

        <input type="hidden" id="cashOnHandAmount" value="₱0.00">

        <div class="w-full overflow-x-auto mt-4">
            <table class="w-full md:w-auto border border-black shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs p6">
                        <th class="p-6 border border-black bg-green-700">CASH ON HAND</th>
                        <th class="p-6 border border-black bg-blue-700">REMITTED</th>
                        <th class="p-6 border border-black bg-yellow-500">RECEIVABLE</th>
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
        <x-scrollable-table height="max-h-[45vh] overflow-y-auto">
            <thead>
                <tr class="bg-[#1a4d2e] text-white border text-center border-black">
                    <th class="p-2 border border-black">ID NUMBER</th>
                    <th class="p-2 border border-black">LASTNAME</th>
                    <th class="p-2 border border-black">FIRSTNAME</th>
                    <th class="p-2 border border-black">YEAR AND BLOCK</th>
                    <th class="p-2 border border-black">BALANCE</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @php $grandTotal = 0; @endphp
                @forelse($students as $student)
                    @php
                        $totalBalance = $payables[$student->student_id]->total_balance ?? 0;
                        $grandTotal += $totalBalance;
                    @endphp
                    <tr class="border border-black cursor-pointer student-row hover:bg-gray-200 text-center"
                        data-yearlevel="{{ strtoupper($student->yearLevel) }}"
                        data-block="{{ strtoupper($student->block) }}"
                        onclick="routeToStudentLedger('{{ $student->student_id }}')">
                        <td class="p-2 border border-black">{{ $student->student_id }}</td>
                        <td class="p-2 border border-black">{{ strtoupper($student->lastname) }}</td>
                        <td class="p-2 border border-black">{{ strtoupper($student->firstname) }}</td>
                        <td class="p-2 border border-black">{{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}</td>
                        <td class="p-2 border text-right text-black border-black balance-cell">₱{{ number_format($totalBalance, 2) }}</td>
                    </tr>
                @empty
                    <tr id="noStudentsRow">
                        <td colspan="5" class="p-2 border border-black text-red-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const rows = document.querySelectorAll('.student-row');
            
                    rows.forEach(row => {
                        row.addEventListener('click', function () {
                            rows.forEach(r => r.classList.remove('bg-gray-300'));
                            this.classList.add('bg-gray-300');
                        });
                    });
                });
            </script>
            
            <tfoot>
                <tr class="font-bold text-center text-white">
                    <td colspan="4" class="p-2 bg-[#1a4d2e] border-black text-center">TOTAL BALANCE:</td>
                    <td class="p-2 border bg-[#1a4d2e] text-right  border-black text-white">₱{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        </x-scrollable-table>
    </div>

    <script>
        function routeToStudentLedger(student_id) {
            window.location.href = "/treasurer/student-ledger/" + student_id;
        }

        const representatives = @json($representatives);
        const cashOnHand = @json($cashOnHand);
        const remitted = @json($remitted);

        const yearLevelFilter = document.getElementById('yearLevelFilter');
        const blockFilter = document.getElementById('blockFilter');
        const repNameDisplay = document.getElementById('representativeName');
        const cashAmount = document.getElementById('cashOnHandAmount');
        const cashTableDisplay = document.getElementById('cashOnHandDisplay');
        const remittedDisplay = document.getElementById('remittedDisplay');
        const receivableDisplay = document.getElementById('receivableDisplay');
        const groupTitle = document.getElementById('groupTitle');
        const studentRows = document.querySelectorAll('.student-row');
        const totalBalanceCell = document.querySelector('tfoot td:last-child');

        yearLevelFilter.addEventListener('change', () => {
            if (yearLevelFilter.value === '') {
                blockFilter.selectedIndex = 0;
            }
            updateDisplay();
        });

        blockFilter.addEventListener('change', updateDisplay);

        function updateDisplay() {
            const year = yearLevelFilter.value.toUpperCase();
            const block = blockFilter.value.toUpperCase();
            const key = year && block ? `${year} - ${block}` : year || block || '';

            groupTitle.textContent = key || 'ALL YEAR LEVEL';
            repNameDisplay.textContent = '';

            let totalCash = 0;
            let totalRemitted = 0;
            let totalBalance = 0;

            if (!year && !block) {
                for (const repName of Object.values(representatives)) {
                    totalCash += parseFloat(cashOnHand[repName] ?? 0);
                    totalRemitted += parseFloat(remitted[repName] ?? 0);
                }
            } else if (year && !block) {
                for (const [repKey, repName] of Object.entries(representatives)) {
                    if (repKey.startsWith(year)) {
                        totalCash += parseFloat(cashOnHand[repName] ?? 0);
                        totalRemitted += parseFloat(remitted[repName] ?? 0);
                    }
                }
            } else if (!year && block) {
                for (const [repKey, repName] of Object.entries(representatives)) {
                    if (repKey.endsWith(block)) {
                        totalCash += parseFloat(cashOnHand[repName] ?? 0);
                        totalRemitted += parseFloat(remitted[repName] ?? 0);
                    }
                }
            } else {
                const repName = representatives[key] || '';
                repNameDisplay.textContent = repName;
                totalCash = parseFloat(cashOnHand[repName] ?? 0);
                totalRemitted = parseFloat(remitted[repName] ?? 0);
            }

            cashAmount.value = `₱${totalCash.toFixed(2)}`;
            cashTableDisplay.textContent = `₱${totalCash.toFixed(2)}`;
            remittedDisplay.textContent = `₱${totalRemitted.toFixed(2)}`;

            studentRows.forEach(row => {
                const rowYear = row.dataset.yearlevel;
                const rowBlock = row.dataset.block;
                const visible = (!year || rowYear === year) && (!block || rowBlock === block);
                row.style.display = visible ? '' : 'none';
                if (visible) {
                    const balance = parseFloat(row.querySelector('td:last-child').innerText.replace(/[₱,]/g, '')) || 0;
                    totalBalance += balance;
                }
            });

            totalBalanceCell.textContent = `₱${totalBalance.toFixed(2)}`;
            receivableDisplay.textContent = `₱${totalBalance.toFixed(2)}`;
        }

        updateDisplay();
    </script>
</div>

</x-trea-components.sidebar>
</x-trea-components.content>
