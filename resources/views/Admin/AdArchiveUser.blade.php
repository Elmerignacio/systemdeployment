<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
    <x-Add-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">
    <div class="mt-4">
                <x-trea-components.content-header>MANAGE USER</x-trea-components.content-header>

            
               <x-trea-components.nav-link>
                <a href="/admin/manageUser" class="text-[17px] font-semibold">Active</a>
                <a href="/admin/archiveUser" class="text-[17px] font-semibold text-green-700 border-b-2 border-green-700 pb-1">Archive</a>
               </x-trea-components.nav-link>
      
            </div>

            <x-trea-components.sorting class="mt-4">
                <a href="createUser" class="bg-[#1a4d2e] text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700">
                    Add Users <i class="fas fa-plus"></i>
                </a>
            </x-trea-components.sorting>
        
                
          
            
         <form action="">
                @csrf
                <div class="mt-4 overflow-auto">
                    <x-trea-components.table class="w-full min-w-[600px]">   
                    <thead>
                        <tr class="bg-[#1a4d2e] text-white border border-black">
                            <th class="p-2 border border-black"><input type="checkbox" id="selectAll"></th>
                            <th class="p-2 border border-black">FIRSTNAME</th>
                            <th class="p-2 border border-black">LASTNAME</th>
                            <th class="p-2 border border-black">GENDER</th>
                            <th class="p-2 border border-black">YEAR AND BLOCK</th>
                            <th class="p-2 border border-black">ROLE</th>
                            <th class="p-2 border border-black">USERNAME</th>
                            <th class="p-2 border border-black">STATUS</th>
                            <th class="p-2 border border-black notClickable">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($archivedStudents as $archived)
                        <tr class="border border-black cursor-pointer" onclick="toggleCheckbox(event, this)">
                            <td class="p-2 border border-black">
                                <input type="checkbox" name="archived[]" value="{{ $archived->student_id }}" class="rowCheckbox">
                            </td>
                            <td class="p-2 border border-black">{{ strtoupper($archived->firstname) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($archived->lastname) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($archived->gender) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($archived->yearLevel) }} - {{ strtoupper($archived->block) }}</td>
                            <td class="p-2 border border-black">{{ strtoupper($archived->role) }}</td>
                            <td class="p-2 border border-black">{{ $archived->username }}</td>
                            <td class="p-2 border border-black text-red-700">{{ strtoupper($archived->status) }}</td>
                            <td class="p-2 border border-black notClickable">
                                <a href="#" class="text-blue-700 px-2 py-1 rounded">EDIT</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                 </x-trea-components.table>
                  
                </div>
            </form>   

            
</x-Add-components.sidebar>
</x-trea-components.content>

