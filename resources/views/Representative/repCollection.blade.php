<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
  <x-Repre-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

    <div class="mt-4">
        <x-trea-components.content-header>COLLECTIONS</x-trea-components.content-header>

   

        <x-trea-components.nav-link>
          <a href="/representative/collection" class="text-[15px] sm:text-[17px] font-semibold text-green-700 border-b-2 border-green-700 pb-1">Payment</a>
         <a href="/representative/remitted" class="text-[15px] sm:text-[17px] text-gray-600"> Remittance</a>
         <a href="/representative/CashOnHand" class="text-[17px] text-gray-600">Cash On Hand</a>
        </x-trea-components.nav-link>
      
              
        <x-trea-components.sorting/>

        

          <div 
            x-data="collectionsApp()" 
            class="flex flex-col md:flex-row overflow-auto"
          >
          <x-two-table-scrollable height="max-h-[45vh] overflow-y-auto">
                <thead>
                  <tr class="bg-green-700 text-white border border-black">
                    <th class="p-2 border border-black">ID NUMBER</th>
                    <th class="p-2 border border-black">FIRSTNAME</th>
                    <th class="p-2 border border-black">LASTNAME</th>
                    <th class="p-2 border border-black">YEAR AND BLOCK</th>
                  </tr>
                </thead>
                <tbody id="usersTableBody" x-data="{ activeRow: null }">
                    @foreach ($students as $student)
                        <tr 
                            class="border border-black cursor-pointer hover:bg-gray-200"
                            :class="activeRow === '{{ $student->student_id }}' ? 'bg-gray-300' : ''"
                            @click="activeRow = '{{ $student->student_id }}'; handleClick('{{ $student->student_id }}', '{{ strtoupper($student->firstname) }} {{ strtoupper($student->lastname) }}', '{{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}')"
                        >
                            <td class="p-2 border border-black">{{ $student->student_id }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($student->firstname) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($student->lastname) }}</td>
                            <td class="p-2 border border-black">
                                {{ strtoupper($student->yearLevel) }} - {{ strtoupper($student->block) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
          </x-two-table-scrollable>

           <x-Repre-components.payment-modal/>

        
    </div>


</x-Repre-components.sidebar>

</x-trea-components.content>

