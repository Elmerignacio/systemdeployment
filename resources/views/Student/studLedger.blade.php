<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
    <x-Student-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">
        <div class="mt-4 text-[#1a4d2e]">
            <x-trea-components.content-header>
                PAYMENT OVERVIEW
            </x-trea-components.content-header>

            <h3 class="text-4xl font-extrabold mt-2 text-[#1a4d2e] pt-4">{{ strtoupper($student->firstname) }} {{ strtoupper($student->lastname) }}</h3>
            <p class="text-gray-700 text-2xl font-medium">ID: {{ $student->student_id }}</p>
            <p class="text-gray-700 font-medium">{{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}</p>
        </div>

        <div class="flex flex-col md:flex-row gap-6 mt-6">
            <div class="flex flex-col space-y-2">
                <h3 class="text-2xl font-bold text-red-700 pt-4 ">
                    REMAINING BALANCE: 
                    
                </h3>

                <x-student-ledger-table>
                    <thead>
                        <tr class="bg-[#1a4d2e] text-white">
                            <th class="p-3 border border-black text-center">DESCRIPTION</th>
                            <th class="p-3 border border-black text-center">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payables as $payable)
                            <tr class="bg-white">
                                <td class="p-3 border border-black text-center">{{ $payable->description }}</td>
                                <td class="p-3 border border-black font-bold text-center">₱{{ number_format($payable->total_balance, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-[#1a4d2e] text-white font-bold text-center">
                            <td class="p-3 border border-black">TOTAL</td>
                            <td class="p-3 border border-black">₱{{ number_format($payables->sum('total_balance'), 2) }}</td>
                        </tr>
                    </tfoot>
                </x-student-ledger-table>
            </div>

            <div class="md:w-2/3">
                <h3 class="text-2xl font-bold text-green-700 mb-2 pt-4">SETTLED PAYABLE:</h3>
                <div class="max-h-96 overflow-y-auto">
                    <table class="w-full table-auto border-separate border-spacing-0 text-center">
                        <thead class="sticky top-0 text-white z-10">
                            <tr>
                                <th class="p-3 border border-black bg-[#1a4d2e]">DATE PAID</th>
                                <th class="p-3 border border-black bg-[#1a4d2e]">DESCRIPTION</th>
                                <th class="p-3 border border-black bg-[#1a4d2e]">AMOUNT</th>
                                <th class="p-3 border border-black bg-[#1a4d2e]">COLLECTED BY</th>
                                <th class="p-3 border border-black bg-[#1a4d2e]">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settledPayables as $settled)
                                <tr class="bg-white">
                                    <td class="p-3 border border-black">
                                        {{ \Carbon\Carbon::parse($settled->date)->format('F d, Y') }}
                                    </td>
                                    <td class="p-3 border border-black">{{ $settled->description }}</td>
                                    <td class="p-3 border border-black font-bold">₱{{ number_format($settled->paid, 2) }}</td>
                                    <td class="p-3 border border-black">{{ $settled->collectedBy ?? 'N/A' }}</td>
                                    <td class="p-3 border border-black font-bold text-wrap break-words 
                                        @if(strtoupper($settled->status) === 'TO TREASURER') text-purple-600 drop-shadow-sm
                                        @elseif(strtoupper($settled->status) === 'COLLECTED BY TREASURER') text-blue-600 drop-shadow-sm
                                        @elseif(strtoupper($settled->status) === 'REMITTED') text-yellow-500 drop-shadow-sm
                                        @elseif(strtoupper($settled->status) === 'COLLECTED') text-green-600 drop-shadow-sm
                                        @else text-red-600 @endif">
                                        {{ strtoupper($settled->status) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="sticky bottom-0 bg-[#1a4d2e] text-white z-10">
                            <tr>
                                <td class="p-3 border border-black text-center font-bold" colspan="2">TOTAL</td>

                                <td class="p-3 border border-black font-bold text-right">₱{{ number_format($settledPayables->sum('paid'), 2) }}</td>
                                <td class="p-3 border border-black"></td>
                                <td class="p-3 border border-black"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </x-Student-components.sidebar>
</x-trea-components.content>
