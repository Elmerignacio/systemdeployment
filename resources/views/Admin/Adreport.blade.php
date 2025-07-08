<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <x-Add-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4">
            <x-trea-components.content-header>REPORTS</x-trea-components.content-header>

            <x-trea-components.nav-link>
                <a href="/admin/report" class="text-[15px] sm:text-[17px] font-semibold text-[#1a4d2e] border-b-2 border-[#1a4d2e] pb-1">Monthly Report</a>
               <a href="/admin/fund" class="text-[15px] sm:text-[17px] text-gray-600"> Funds</a>
              </x-trea-components.nav-link>
            

            <x-trea-components.sorting>
            </x-trea-components.sorting>

            <div x-data="remittanceApp({{ $remittanceRecords->toJson() }})" class="flex flex-col md:flex-row">
        
                <x-two-table-scrollable>
                    <thead>
                        <tr class="bg-white border border-black">
                            <th class="p-2 border border-black bg-[#1a4d2e] text-center text-white">YEAR AND BLOCK</th>
                            <th class="p-2 border border-black bg-[#1a4d2e] text-center text-white">TOTAL RECEIVABLE</th>
                            <th class="p-2 border border-black bg-[#1a4d2e] text-center text-white">TOTAL REMITTED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedData as $data)
                            <tr @click="openDetails('{{ $data->year_and_block }}')" class="cursor-pointer hover:bg-gray-200 transition-all duration-300 ease-in-out">
                                <td class="p-2 border border-black">{{ $data->year_and_block }}</td>
                                <td class="p-2 border border-black text-black">{{ number_format($data->total_receivable, 2) }}</td>
                                <td class="p-2 border border-black">{{ number_format($data->total_remitted, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-2 text-center text-red-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="p-2 text-white border border-black bg-[#1a4d2e] text-center text-xl font-bold">Total</td>
                            <td class="p-2 border border-black font-bold bg-[#1a4d2e] text-right text-xl text-white">{{ number_format($groupedData->sum('total_receivable'), 2) }}</td>
                            <td class="p-2 border border-black font-bold bg-[#1a4d2e] text-right text-xl text-white">{{ number_format($groupedData->sum('total_remitted'), 2) }}</td>
                        </tr>
                    </tfoot>
                </x-two-table-scrollable>

                <div x-show="showDetails" x-transition:enter="transition duration-300 transform" x-transition:enter-start="-translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition duration-200 transform" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-10 opacity-0" class="h-[100%] w-full md:w-1/2 mx-auto p-6 mt-4 bg-gray-300 bg-opacity-40 shadow-lg border-2 border-green-700 rounded-lg relative">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-[#1a4d2e]">
                            Details for <span x-text="selectedYearBlock"></span>
                        </h2>
                        <button @click="showDetails = false" class="text-red-600 font-bold">X</button>
                    </div>

                    <span id="treasurerName" class="hidden">{{ $treasurer->firstname }} {{ $treasurer->lastname }}</span>
                    <span id="adminName" class="hidden">{{ $admin->firstname }} {{ $admin->lastname }}</span>
                    
                    <div class="mb-2 flex justify-left">
                        <select x-model="selectedDescription" class="w-1/3 p-2 border border-[#1a4d2e] rounded">
                            <option value="" disabled selected>Select Description</option>
                            <template x-for="desc in [...new Set(remittances.filter(r => r.yearLevel + ' - ' + r.block === selectedYearBlock).map(r => r.description))]" :key="desc">
                                <option x-text="desc" :value="desc"></option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-2 flex justify-left mt-2">
                        <select x-model="selectedMonth" class="w-1/3 p-2 border border-[#1a4d2e] rounded">
                            <option value="" disabled selected>ALL MONTH</option>
                            <template x-for="month in Array.from({length: 12}, (_, i) => i + 1)">
                                <option :value="month" x-text="new Date(0, month - 1).toLocaleString('en-US', { month: 'long' })"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Remittance Table -->
                    <x-scrollable-table height="max-h-[40vh]">
                        <thead>
                            <tr class="bg-[#1a4d2e] text-white text-center">
                                <th class="p-2 border border-black">DATE REMITTED</th>
                                <th class="p-2 border border-black">PAID</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-center">
                            <template x-if="selectedDescription">
                                <template x-for="item in groupedByDate" :key="item.date">
                                    <tr @click="openModal(item)" class="cursor-pointer hover:bg-gray-100">
                                        <td class="border p-2 text-center" x-text="item.date"></td>
                                        <span id="printDate" x-text="item.date" class="hidden"></span>
                                        <td class="border p-2 text-center" x-text="`₱${item.paid.toFixed(2)}`"></td>
                                    </tr>
                                </template>
                            </template>
                            
                            <template x-if="!filteredRemittances.length">
                                <tr>
                                    <td colspan="2" class="p-2 text-center text-red-500">No data available for this description</td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot x-show="filteredRemittances.length">
                            <tr>
                                <td class="p-2 text-white border border-black text-center font-bold">Total Paid</td>
                                <td class="p-2 border border-black font-bold text-white text-center">
                                    ₱<span x-text="filteredRemittances.reduce((sum, r) => sum + parseFloat(r.paid), 0).toFixed(2)"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </x-scrollable-table>
                </div>

                <!-- Modal -->
                <div x-show="modalOpen" x-transition class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50" style="display: none;">
                    <div class="modal bg-white rounded-md w-full md:w-2/3 lg:w-1/2 p-6 relative text-white shadow-lg">
                        <div class="flex justify-end mb-4">
                            <button @click="closeModal" class="text-[#1a4d2e] text-xl font-bold">✕</button>
                        </div>
                        
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-black" id="specificDescription"></h3>
                            <p class="text-lg font-semibold text-black" id="selectedYearBlock"></p> 
                            <p class="text-lg font-semibold text-black" id="printDate"></p> 
                        </div>

                        <div class="overflow-y-auto max-h-[50vh]">
                            <table class="w-full bg-[#1a4d2e] text-white border border-white text-sm">
                                <thead>
                                    <tr class="text-center bg-[#1a4d2e]">
                                        <th class="p-2 border border-black">NAME</th>
                                        <th class="p-2 border border-black">AMOUNT PAID</th>
                                    </tr>
                                </thead>
                                <tbody id="remittancesTable" class="bg-white text-black"></tbody>
                                <tfoot>
                                    <tr class="bg-[#1a4d2e] text-center font-bold">
                                        <td class="p-2 border border-black">TOTAL</td>
                                        <td class="p-2 border border-black" id="totalAmount"></td> 
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="btn flex justify-center gap-4 mt-6">
                            <button @click="printModal" class="bg-[#1a4d2e] text-white px-4 py-2 rounded hover:bg-gray-200 font-bold">🖨 PRINT</button>
                            <button @click="exportModal" class="bg-[#1a4d2e] text-white px-4 py-2 rounded hover:bg-gray-200 font-bold">📁 EXPORT</button>
                        </div>
                    </div>
                </div>
            </div>
        </x-Add-components.sidebar>
    </x-trea-components.content>

    <script>
        function remittanceApp(remittances) {
            return {
                showDetails: false,
                modalOpen: false,
                selectedYearBlock: '',
                selectedDescription: '',
                selectedMonth: '',
                selectedDate: '', 
                remittances,
    
                get filteredRemittances() {
                    return this.remittances.filter(r => {
                        const blockMatch = `${r.yearLevel} - ${r.block}` === this.selectedYearBlock;
                        const descMatch = r.description === this.selectedDescription;
                        const monthMatch = this.selectedMonth ? (new Date(r.date).getMonth() + 1) === +this.selectedMonth : true;
                        return blockMatch && descMatch && monthMatch;
                    });
                },
    
                get groupedByDate() {
                    const grouped = {};
                    this.filteredRemittances.forEach(r => {
                        const date = new Date(r.date);
                        const dateKey = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
    
                        if (!grouped[dateKey]) {
                            grouped[dateKey] = {
                                ...r,
                                date: dateKey,
                                paid: parseFloat(r.paid)
                            };
                        } else {
                            grouped[dateKey].paid += parseFloat(r.paid);
                        }
                    });
    
                    return Object.values(grouped);
                },
    
                openDetails(block) {
                    this.selectedYearBlock = block;
                    this.showDetails = true;
                    this.selectedDescription = '';
                    this.selectedMonth = '';
                },
    
                openModal(item) {
                    const filtered = this.remittances.filter(r => {
                        return r.yearLevel === item.yearLevel &&
                               r.block === item.block &&
                               r.description === item.description &&
                               new Date(r.date).toLocaleDateString() === new Date(item.date).toLocaleDateString();
                    });
    
                    const tableBody = document.getElementById('remittancesTable');
                    tableBody.innerHTML = '';
                    let total = 0;
    
                    filtered.forEach(r => {
                        const row = document.createElement('tr');
                        row.classList.add('text-center');
                        row.innerHTML = `
                            <td class="p-2 border border-black">${r.firstName} ${r.lastName}</td>
                            <td class="p-2 border border-black">₱${parseFloat(r.paid).toFixed(2)}</td>
                        `;
                        tableBody.appendChild(row);
                        total += parseFloat(r.paid);
                    });
    
                    document.getElementById('specificDescription').textContent = item.description;
                    document.getElementById('selectedYearBlock').textContent = item.yearLevel + ' - ' + item.block;
                    document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
                    document.getElementById('printDate').textContent = item.date;
                    this.selectedDate = item.date; 
                    this.modalOpen = true;
                },
    
                closeModal() {
                    this.modalOpen = false;
                },
    
                printModal() {
                    const printWindow = window.open('', '', 'height=1000,width=1000');
                    printWindow.document.write('<html><head><title>Remittance Report</title>');
                    printWindow.document.write('<style>');
                    printWindow.document.write(`
                        body { font-family: Arial, sans-serif; margin:0;}
                        .modal { display: block; }
                        .btn { display: none; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0;}
                        table, th, td { border: 1px solid black; }
                        th, td { padding: 10px; text-align: left; }
                        .footer { display: none; }
                        .signature-table { width: 100%; margin-top: 60px; border: none; }
                        .signature-table td { text-align: center; border: none; padding-top: 40px; }
                    `);
                    printWindow.document.write('</style>');
                    printWindow.document.write('</head><body>');
    
                    printWindow.document.write(`
                        <h3 style="text-align:center;">BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY</h3>
                        <h4 style="margin-top:50px;">Description: ${document.getElementById('specificDescription').textContent}</h4> 
                        <h4>Year and Block: ${document.getElementById('selectedYearBlock').textContent}</h4>
                        <h4>Date: ${this.selectedDate}</h4> <!-- ✅ Print the date here -->
    
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody id="remittancesTable">
                                ${document.getElementById('remittancesTable').innerHTML}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total Paid</strong></td>
                                    <td><strong>₱${document.getElementById('totalAmount').textContent.replace('₱', '')}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
    
                        <table style="width:100%; margin-top: 100px; border: none;">
                            <tr>
                                <td style="text-align: center; border: none;">
                                    ${document.getElementById('treasurerName').textContent}<br>
                                    <span style="display: inline-block; width: 150px; border-bottom: 1px solid #000; margin: 4px 0;"></span><br>
                                    TREASURER
                                </td>
                                <td style="text-align: center; border: none;">
                                    ${document.getElementById('adminName').textContent}<br>
                                    <span style="display: inline-block; width: 180px; border-bottom: 1px solid #000; margin: 4px 0;"></span><br>
                                    ADMIN
                                </td>
                            </tr>
                        </table>
                    `);
    
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
    
                    printWindow.onload = function () {
                        printWindow.print();
                    };
                },
    
                exportModal() {
                    const description = document.getElementById('specificDescription').textContent;
                    const yearBlock = document.getElementById('selectedYearBlock').textContent;
                    const rows = document.querySelectorAll('#remittancesTable tr');
    
                    const wsData = [];
                    wsData.push(["Description", "Year and Block", "Name", "Amount Paid"]);
    
                    rows.forEach(row => {
                        const cols = row.querySelectorAll('td');
                        if (cols.length === 2) {
                            const name = cols[0].textContent.trim();
                            const amount = cols[1].textContent.trim().replace('₱', '');
                            wsData.push([description, yearBlock, name, '₱' + amount]);
                        }
                    });
    
                    const totalAmount = Array.from(rows).reduce((sum, row) => {
                        const cols = row.querySelectorAll('td');
                        if (cols.length === 2) {
                            const amount = parseFloat(cols[1].textContent.trim().replace('₱', '').replace(',', ''));
                            sum += amount;
                        }
                        return sum;
                    }, 0);
    
                    wsData.push(["", "", "Total", '₱' + totalAmount.toFixed(2)]);
                    const ws = XLSX.utils.aoa_to_sheet(wsData);
    
                    const headerStyle = {
                        font: { bold: true, sz: 14 },
                        alignment: { horizontal: "center", vertical: "center" },
                        border: { top: { style: 'thin' }, bottom: { style: 'thin' }, left: { style: 'thin' }, right: { style: 'thin' } },
                        fill: { bgColor: { rgb: "FFFF99" } }
                    };
    
                    for (let col = 0; col < wsData[0].length; col++) {
                        const cellRef = XLSX.utils.encode_cell({ r: 0, c: col });
                        if (!ws[cellRef]) ws[cellRef] = {};
                        ws[cellRef].s = headerStyle;
                    }
    
                    const dataCellStyle = {
                        font: { sz: 12 },
                        alignment: { horizontal: "center", vertical: "center" },
                        border: { top: { style: 'thin' }, bottom: { style: 'thin' }, left: { style: 'thin' }, right: { style: 'thin' } },
                        fill: { bgColor: { rgb: "D3F8E2" } }
                    };
    
                    for (let r = 1; r < wsData.length; r++) {
                        for (let c = 0; c < wsData[r].length; c++) {
                            const cellRef = XLSX.utils.encode_cell({ r: r, c: c });
                            if (!ws[cellRef]) ws[cellRef] = {};
                            ws[cellRef].s = dataCellStyle;
                        }
                    }
    
                    const footerStyle = {
                        font: { bold: true, sz: 12 },
                        alignment: { horizontal: "center", vertical: "center" },
                        border: { top: { style: 'thin' }, bottom: { style: 'thin' }, left: { style: 'thin' }, right: { style: 'thin' } },
                        fill: { bgColor: { rgb: "FFD700" } }
                    };
    
                    const footerRowIndex = wsData.length - 1;
                    for (let col = 0; col < wsData[footerRowIndex].length; col++) {
                        const cellRef = XLSX.utils.encode_cell({ r: footerRowIndex, c: col });
                        if (!ws[cellRef]) ws[cellRef] = {};
                        ws[cellRef].s = footerStyle;
                    }
    
                    const wscols = [{ wch: 30 }, { wch: 30 }, { wch: 40 }, { wch: 30 }];
                    ws['!cols'] = wscols;
    
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Remittance Report");
    
                    XLSX.writeFile(wb, `remittance_report_${Date.now()}.xlsx`);
                }
            };
        }
    </script>
    
    
    
