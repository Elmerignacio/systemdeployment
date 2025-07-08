<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
<x-Repre-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">

    <div class="mt-4">
        <x-trea-components.content-header>
            <a href="javascript:void(0);" class="back-link" onclick="goBack()">
              <i class="fas fa-arrow-left hover:text-blue-500"></i>
            </a>
            STUDENT LEDGER
        </x-trea-components.content-header>
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
        
        
        <h3 class="text-2xl font-extrabold mt-2">{{ strtoupper($student->firstname) }} {{ strtoupper($student->lastname) }}</h3>
        <p class="text-gray-700 font-medium">ID: {{ $student->student_id }}</p>
        <p class="text-gray-700 font-medium">{{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}</p>
    </div>
    
  <div class="flex flex-col md:flex-row gap-6 mt-6">
    <div class="flex flex-col space-y-2">
      <h3 class="text-lg font-bold text-green-900">
          REMAINING BALANCE: 
          <span class="text-black">₱{{ number_format($payables->sum('total_balance'), 2) }}</span>
      </h3>
  
      <x-student-ledger-table>
          <thead>
              <tr class="bg-green-700 text-white">
                  <th class="p-3 border border-black text-center">DESCRIPTION</th>
                  <th class="p-3 border border-black text-center">AMOUNT</th>
              </tr>
          </thead>
          <tbody>
              @foreach($payables as $payable)
                  <tr>
                      <td class="p-3 border border-black text-center">{{ $payable->description }}</td>
                      <td class="p-3 border border-black font-bold text-center">₱{{ number_format($payable->total_balance, 2) }}</td>
                  </tr>
              @endforeach
          </tbody>
          <tfoot>
              <tr class="bg-green-700 text-white text-center font-bold">
                  <td class="p-3 border border-black">TOTAL</td>
                  <td class="p-3 border border-black">₱{{ number_format($payables->sum('total_balance'), 2) }}</td>
              </tr>
          </tfoot>
      </x-student-ledger-table>
    </div>

    <div class="flex flex-col space-y-2">
      <h3 class="text-lg font-bold text-green-900">SETTLED PAYABLE
      </h3>

      <x-student-ledger-table width="w-full md:w-[1300px]">
          <thead class="text-white text-center">
            <tr>
              <th class="p-3 border border-black bg-green-700">Date</th>
              <th class="p-3 border border-black bg-green-700">Description</th>
              <th class="p-3 border border-black bg-green-700">Amount</th>
              <th class="p-3 border border-black bg-green-700">Collected By</th>
              <th class="p-3 border border-black bg-green-700">Status</th>
            </tr>
          </thead>
          <tbody class="text-center">
            @foreach($settledPayables as $settled)
              <tr>
                <td class="p-3 border border-black">
                  {{ \Carbon\Carbon::parse($settled->date)->format('F d, Y') }}
                </td>
                <td class="p-3 border border-black">
                  {{ $settled->description }}
                </td>
                <td class="p-3 border border-black font-bold">
                  ₱{{ number_format($settled->paid, 2) }}
                </td>
                <td class="p-3 border border-black">
                  {{ $settled->collectedBy ?? 'N/A' }}
                </td>
                <td class="p-3 border border-black font-bold text-wrap break-words 
                    @if(strtoupper($settled->status)==='TO TREASURER') text-orange-500 drop-shadow-sm
                    @elseif(strtoupper($settled->status)==='COLLECTED BY TREASURER') text-blue-600 drop-shadow-sm
                    @elseif(strtoupper($settled->status)==='REMITTED') text-green-600 drop-shadow-sm
                    @elseif(strtoupper($settled->status)==='COLLECTED') text-yellow-600 drop-shadow-sm
                    @else text-red-600 @endif">
                  {{ strtoupper($settled->status) }}
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="sticky bottom-0 bg-green-700 text-white text-center z-10">
            <tr>
              <td class="p-3 border border-black text-left font-bold">TOTAL</td>
              <td class="p-3 border border-black"></td>
              <td class="p-3 border border-black font-bold">
                ₱{{ number_format($settledPayables->sum('paid'), 2) }}
              </td>
              <td class="p-3 border border-black"></td>
              <td class="p-3 border border-black"></td>
            </tr>
          </tfoot>
          </x-student-ledger-table>
    

        
        </div>
    </div>

  
</x-Repre-components.sidebar>
</x-trea-components.content>


