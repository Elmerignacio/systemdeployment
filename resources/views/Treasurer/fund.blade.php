<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <x-trea-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4">
            <x-trea-components.content-header>REPORTS</x-trea-components.content-header>

            <x-trea-components.nav-link>
                <a href="/treasurer/report" class="text-[15px] sm:text-[17px] text-gray-600"> Monthly Report</a>
                <a href="/treasurer/fund" class="text-[15px] sm:text-[17px] font-semibold text-[#1a4d2e] border-b-2 border-[#1a4d2e] pb-1">Funds</a>
            </x-trea-components.nav-link>

            <x-trea-components.sorting>
            </x-trea-components.sorting>

            <div x-data="remittanceApp({{ $remittanceRecords->toJson() }})" class="flex flex-col md:flex-row">

                <div class="w-full">
                    <!-- Begin Printable Area -->
                    <div id="printArea">
                        <table class="mt-4 w-full table-fixed border border-black">
                            <thead>
                                <tr class="bg-white text-center">
                                    <th class="p-2 border border-black bg-[#1a4d2e] text-white">DESCRIPTION</th>
                                    <th class="p-2 border border-black bg-red-700 text-white">EXPENSES</th>
                                    <th class="p-2 border border-black text-white bg-green-700">CASH ON HAND</th>
                                </tr>
                            </thead>
                        </table>

                        <div class="max-h-60 overflow-y-auto">
                            <table class="w-full table-fixed border border-black">
                                <tbody>
                                    @foreach($expensesWithDescriptions as $expense)
                                        <tr class="text-center">
                                            <td class="p-2 border border-black">{{ $expense->description }}</td>
                                            <td class="p-2 border text-right border-black text-black ">₱{{ number_format($totalExpenses, 2) }}</td>
                                            <td class="p-2 border text-right border-black text-black">₱{{ number_format($cashOnHand, 2) }}</td>
                                            <span id="treasurerName" style="display: none;">{{ $treasurer->firstname }} {{ $treasurer->lastname }}</span>
                                            <span id="adminName" style="display: none;">{{ $admin->firstname }} {{ $admin->lastname }}</span>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <table class="w-full table-fixed border border-black">
                            <tfoot>
                                <tr class="bg-[#1a4d2e] text-center text-xl font-bold">
                                    <td class="p-2 border border-black text-white ">TOTAL</td>
                                    <td class="p-2 border border-black text-white text-right bg-red-700">₱{{ number_format($totalExpenses, 2) }}</td>
                                    <td class="p-2 border border-black text-white text-right bg-green-700">₱{{ number_format($cashOnHand, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="flex justify-end gap-4 mt-6 no-print">
                        <button @click="printSummary" class="bg-[#1a4d2e] text-white px-4 py-2 rounded hover:bg-gray-200 font-bold">🖨 PRINT</button>
                        <button @click="exportSummary" class="bg-[#1a4d2e] text-white px-4 py-2 rounded hover:bg-gray-200 font-bold">📁 EXPORT</button>
                    </div>
                </div>

            </div>
        </div>
    </x-trea-components.sidebar>
</x-trea-components.content>


<script>
    function remittanceApp(remittances) {
        return {
          printSummary() {
    const printContents = document.getElementById('printArea').innerHTML;

    // Get names from the current page DOM
    const treasurerName = document.getElementById('treasurerName').textContent;
    const adminName = document.getElementById('adminName').textContent;

    const printWindow = window.open('', '', 'height=1000,width=1000');
    printWindow.document.write('<html><head><title>BSIT Fund Summary Report</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('table { width: 100%; border: 1px solid black; border-collapse: collapse; }');
    printWindow.document.write('th, td { border: 1px solid black; padding: 10px; text-align: left; font-family: Arial, sans-serif; font-size: 14px; vertical-align: middle; }');
    printWindow.document.write('th { font-weight: bold; }');
    printWindow.document.write('thead, tbody, tfoot { border: 1px solid black; border-collapse: collapse; }');
    printWindow.document.write('tbody tr { border-bottom: 1px solid black; }');
    printWindow.document.write('tbody tr:last-child { border-bottom: none; }');
    printWindow.document.write('tbody tr:nth-child(even) { background-color: #f9f9f9; }');
    printWindow.document.write('tbody tr:hover { background-color: #f1f1f1; }');
    printWindow.document.write('@media print { table { border: 1px solid black; border-collapse: collapse; } th, td { padding: 10px; font-size: 12px; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3 style="text-align: center; font-family: Arial, sans-serif;">BSIT Fund Summary Report</h3>');
    printWindow.document.write(printContents);

    printWindow.document.write(`
    <div style="width: 100%; display: flex; justify-content: space-around; margin-top: 100px; font-family: Arial, sans-serif; font-size: 14px;">
        <div style="text-align: center;">
        PREPARED BY: <br><br><br><br>
            ${treasurerName}<br>
            <span style="display: inline-block; width: 150px; border-bottom: 1px solid #000; margin: 4px 0;"></span><br>
            DEPARTMENT TREASURER
        </div>
        <div style="text-align: center;">
        NOTED BY: <br><br><br><br>
            ${adminName}<br>
            <span style="display: inline-block; width: 180px; border-bottom: 1px solid #000; margin: 4px 0;"></span><br>
            DEPARTMENT HEAD
        </div>
    </div>
    `);

    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.onload = function () {
        printWindow.print();
    };
},

            exportSummary() {
                const rows = document.querySelectorAll('#printArea table tbody tr, #printArea table tfoot tr');
                const wsData = [["Description", "Expenses", "Cash on Hand"]];

                rows.forEach(row => {
                    const cols = row.querySelectorAll('td');
                    if (cols.length === 3) {
                        wsData.push([
                            cols[0].textContent.trim(),
                            cols[1].textContent.trim(),
                            cols[2].textContent.trim()
                        ]);
                    }
                });

                const ws = XLSX.utils.aoa_to_sheet(wsData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "CIT FUNDS");

                XLSX.writeFile(wb, `expenses_summary_${Date.now()}.xlsx`);
            }
        };
    }
</script>
