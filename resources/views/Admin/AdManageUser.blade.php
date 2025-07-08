<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
<x-Add-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">

    <div class="mt-3">
        <x-trea-components.content-header>MANAGE USER</x-trea-components.content-header>
        
        <x-trea-components.nav-link class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="/admin/manageUser" class="text-[17px] font-semibold text-[#1a4d2e] border-b-2 border-green-700 pb-1">Active</a>
            <a href="/admin/archiveUser" class="text-[17px] text-gray-600">Archive</a>
        </x-trea-components.nav-link>
             
        <x-trea-components.sorting class="mt-4">
            <button id="archiveBtn" class="bg-[#6B7280] text-white px-6 py-2 mr-[350px] rounded-lg shadow-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled title="Select at least one student to enable">
                Archive
            </button>
        
            <a href="#" onclick="openModal()" class="bg-[#1a4d2e] text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700">
                Add Users <i class="fas fa-plus"></i>
            </a>
            
        </x-trea-components.sorting>
        <script>
            function openModal() {
                document.getElementById("createUserModal").classList.remove("hidden");
            }
            
            function closeModal() {
                document.getElementById("createUserModal").classList.add("hidden");
            }
        </script>

<div x-data="{  
        showDetails: false,
        selectedPayable: {
          id: '',
          student_id: '',
          firstname: '',
          lastname: '',
          yearLevel: '',
          block: '',
          gender: '',
          description: '',
          amount: '',
          dueDate: '',
          profile_url: '',     
          defaultImage: '{{ asset("storage/images/1.jpg") }}'
        },
        selectUser(user) {
          this.selectedPayable = user;
          this.showDetails = true;
        }
      }" class="flex flex-col md:flex-row overflow-auto">

                <x-two-table-scrollable>
                    <thead class="sticky top-0 z-10 bg-[#1a4d2e] text-white text-sm sm:text-base">
                        <tr>
                            <th class="p-2 border border-black text-center"><input type="checkbox" id="selectAll"
                                    class="accent-green-700"></th>
                            <th class="p-2 border border-black text-center">ID NUMBER</th>
                            <th class="p-2 border border-black text-center">FIRSTNAME</th>
                            <th class="p-2 border border-black text-center">LASTNAME</th>
                            <th class="p-2 border border-black text-center">YEAR & BLOCK</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" x-data="{ selectedRow: null }">
                        @foreach ($studentsWithProfile as $student)
                                    <tr :class="selectedRow === '{{ $student->student_id }}' ? 'bg-gray-300' : 'hover:bg-gray-200'"
                                        class="border border-black cursor-pointer text-sm sm:text-base transition-colors duration-150"
                                        @click="
                              selectedRow = '{{ $student->student_id }}';
                              selectUser({
                                id: '{{ $student->student_id }}',
                                student_id: '{{ $student->student_id }}',
                                firstname: '{{ strtoupper($student->firstname) }}',
                                lastname: '{{ strtoupper($student->lastname) }}',
                                yearLevel: '{{ strtoupper($student->yearLevel) }}',
                                block: '{{ strtoupper($student->block) }}',
                                gender: '{{ ucfirst(strtolower($student->gender)) }}',
                                description: '',
                                amount: '',
                                dueDate: '',
                                profile_url: '{{ $student->profile_url !== null ? $student->profile_url : "" }}'
                              })
                            ">
                                        <td class="p-2 border border-black text-center"><input type="checkbox" name="students[]"
                                                value="{{ $student->student_id }}" class="rowCheckbox accent-[#1a4d2e]"></td>
                                        <td class="p-2 border border-black">{{ $student->student_id }}</td>
                                        <td class="p-2 border border-black">{{ strtoupper($student->firstname) }}</td>
                                        <td class="p-2 border border-black">{{ strtoupper($student->lastname) }}</td>
                                        <td class="p-2 border border-black">{{ strtoupper($student->yearLevel) }} -
                                            {{ strtoupper($student->block) }}</td>
                                    </tr>
                        @endforeach
                    </tbody>
                </x-two-table-scrollable>

                <x-trea-components.modify-table>
                    <div class="relative flex justify-center">
                        <img id="previewImage" class="w-32 h-32  rounded-full border-2 border-green-700"
                            :src="!selectedPayable . profile_url || selectedPayable . profile_url === 'null' ? selectedPayable . defaultImage : selectedPayable . profile_url" alt="User Profile" />
                    </div>
                </x-trea-components.modify-table>
            </div>
    </div>
    
    
        
         
<x-Add-components.create-users/>

<x-trea-components.archive-modal/>
<x-trea-components.archive-success-modal/>


</x-Add-components.sidebar>
</x-trea-components.content>
