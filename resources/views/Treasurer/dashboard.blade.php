<x-trea-components.layout />
<x-trea-components.header />
<x-trea-components.content>

<x-trea-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">
    <div class="space-y-4">
        <h2 class="text-4xl font-bold text-[#1a4d2e]">WELCOME, {{ $firstname }} {{ $lastname }}!</h2>
        <p class="text-xl text-gray-600 font-semibold">DEPARTMENT: {{ $role }}</p>
    </div>

    <div class="grid gap-6 mt-8 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1">
        <!-- Cash on Hand -->
        <div class="bg-[#1a4d2e] text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="{{ asset('images/cashonhand.png') }}" alt="Cash on Hand Icon" class="w-12 h-12">
                <span class="text-xl font-bold drop-shadow-md">CASH ON HAND</span>
            </div>
            <p class="text-4xl font-extrabold text-center drop-shadow-md">₱{{ number_format($cashOnHand, 2) }}</p>
        </div>

        <!-- Expenses -->
        <div class="bg-red-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="{{ asset('images/money.png') }}" alt="Expenses Icon" class="w-12 h-12">
                <span class="text-xl font-bold drop-shadow-md">EXPENSES</span>
            </div>
            <p class="text-4xl font-extrabold text-center drop-shadow-md">₱{{ number_format($totalExpenses, 2) }}</p>
        </div>

        <!-- Receivables -->
        <div class="bg-yellow-500 text-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="{{ asset('images/receive.png') }}" alt="Receivables Icon" class="w-12 h-12">
                <span class="text-xl font-bold drop-shadow-md">RECEIVABLE</span>
            </div>
            <p class="text-4xl font-extrabold text-center drop-shadow-md">₱{{ number_format($totalAmount, 2) }}</p>
        </div>
    </div>

    <!-- Students Payables -->
    <div class="mt-10 pb-6">
        <h3 class="text-2xl font-bold text-[#1a4d2e] mb-4">STUDENTS PAYABLES</h3>

        <x-scrollable-table height="max-h-[45vh] overflow-y-auto">
            <thead class="bg-[#1a4d2e] text-white border border-black">
                <tr class="text-center">
                    <th class="border border-gray-300 p-2 w-[300px]">DESCRIPTION</th>
                    <th class="border border-gray-300 p-2 w-[300px]">AMOUNT</th>
                    <th class="border border-gray-300 p-2 w-[300px]">EXPECTED RECEIVABLE</th>
                    <th class="border border-gray-300 p-2 w-[300px]">DUE DATE</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @foreach($Payables as $payable)
                    <tr class="border border-black hover:bg-gray-100 cursor-pointer"
                        @click="selectedPayable = {
                            description: '{{ $payable->description }}',
                            amount: '{{ number_format(floor($payable->input_balance), 2) }}',
                            dueDate: '{{ $payable->dueDate }}',
                            yearLevel: '{{ $payable->yearLevel ?? '' }}',
                            block: '{{ $payable->block ?? '' }}',
                            name: '{{ $payable->name ?? '' }}'
                        }; showDetails = true">
                        <td class="text-center p-2 border border-black">{{ $payable->description }}</td>
                        <td class="text-right p-2 border border-black">₱{{ number_format(floor($payable->input_balance), 2) }}</td>
                        <td class="text-right p-2 border border-black">₱{{ number_format(floor($payable->expected_receivable), 2) }}</td>
                        <td class="text-center p-2 border border-black">{{ \Carbon\Carbon::parse($payable->dueDate)->format('F d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-scrollable-table>
    </div>
</x-trea-components.sidebar>

</x-trea-components.content>
