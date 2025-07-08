<div x-show="showDetails"
    x-transition:enter="transition duration-300 transform"
    x-transition:enter-start="-translate-y-10 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition duration-200 transform"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="-translate-y-10 opacity-0"
    class="h-[100%] w-full md:w-1/2 mx-auto p-3 mt-4  bg-gray-300 bg-opacity-40 shadow-lg border-2 border-green-700 rounded-lg relative"
>
    <button 
        class="absolute top-2 right-2 text-green-700 hover:text-red-500 p-4"
        @click="showDetails = false"
    >
        <i class="fas fa-times text-lg"></i>
    </button>

    <div class="relative">
        <form action="{{ route('modify.users') }}" method="POST">
            @csrf
            {{ $slot }}
        
            <div class="flex flex-col md:flex-row gap-4 mt-5">
                <div class="w-full md:w-1/2">
                    <label class="block">ID NUMBER:</label>
                    <input 
                        type="text" 
                        name="students[]" 
                        readonly
                        x-model="selectedPayable.id" 
                        @input="selectedPayable.id = $event.target.value.toUpperCase()" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block">GENDER:</label>
                    <select 
                        name="gender" 
                        x-model="selectedPayable.gender" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                        <option value="" disabled>Select gender</option>
                        <option value="Male">MALE</option>
                        <option value="Female">FEMALE</option>
                    </select>
                </div>
            </div>
        
            <div class="flex flex-col md:flex-row gap-4 mt-2">
                <div class="w-full md:w-1/2">
                    <label class="block">FIRSTNAME:</label>
                    <input 
                        type="text" 
                        name="firstname" 
                        x-model="selectedPayable.firstname" 
                        @input="selectedPayable.firstname = $event.target.value.toUpperCase()" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block">LASTNAME:</label>
                    <input 
                        type="text" 
                        name="lastname" 
                        x-model="selectedPayable.lastname" 
                        @input="selectedPayable.lastname = $event.target.value.toUpperCase()" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                </div>
            </div>
        
            <div class="flex flex-col md:flex-row gap-4 mt-2">
                <div class="w-full md:w-1/2">
                    <label class="block">YEAR LEVEL:</label>
                    <select 
                        name="yearLevel" 
                        x-model="selectedPayable.yearLevel" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                        <option value="" disabled>Select Year Level</option>
                        <option value="1ST YEAR">1ST YEAR</option>
                        <option value="2ND YEAR">2ND YEAR</option>
                        <option value="3RD YEAR">3RD YEAR</option>
                        <option value="4TH YEAR">4TH YEAR</option>
                    </select>
                    <div x-show="!selectedPayable.yearLevel" class="text-red-500 mt-2">Data has been lost.</div>
                </div>
        
                <div class="w-full md:w-1/2">
                    <label class="block">BLOCK:</label>
                    <select 
                        name="block" 
                        x-model="selectedPayable.block" 
                        class="w-full p-2 border-2 border-green-700 text-black rounded-md focus:ring-0 focus:outline-none"
                    >
                        <option value="" disabled>Select Block</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                    </select>
                </div>
            </div>
        
            <div class="flex flex-col md:flex-row justify-center mt-6 gap-10">

            <button 
                type="button" 
                id="archiveBTN"
                class="bg-red-600 px-4 py-2 rounded-md text-white font-bold w-full md:w-auto"
            >
                ARCHIVE
            </button>
             <x-trea-components.archive-specific-student-modal/>  
             
          <!-- MODIFY Button -->
        <button 
            type="button" 
            id="modifyBTN"
            class="bg-green-700 px-4 py-2 rounded-md text-white font-bold w-full md:w-auto"
        >
            MODIFY
        </button>

       <x-trea-components.modify-specific-student-modal/>  


            </div>
        </form>
    </div> 
</div>


<script>
    document.getElementById('archiveBTN')?.addEventListener('click', function(event) {
        event.preventDefault();
        let archiveMessage = document.getElementById('archiveMessage');
        archiveMessage?.classList.remove('hidden');   
    });

    document.getElementById('CancelButton')?.addEventListener('click', function(event) {
        event.preventDefault();
        let successMessage = document.getElementById('successMessage');
        let archiveMessage = document.getElementById('archiveMessage');
        successMessage?.classList.add('hidden');
        archiveMessage?.classList.add('hidden');
    });

    document.getElementById('archiveButton')?.addEventListener('click', function(event) {
        event.preventDefault();
        let successMessage = document.getElementById('successMessage');
        let archiveMessage = document.getElementById('archiveMessage');
        successMessage?.classList.remove('hidden');
        archiveMessage?.classList.add('hidden');
    });
</script>

<script>
    document.getElementById('archiveBTN')?.addEventListener('click', function() {
        let idNumber = document.querySelector('input[name="students[]"]')?.value;
        let firstname = document.querySelector('input[name="firstname"]')?.value;
        let lastname = document.querySelector('input[name="lastname"]')?.value;
        let yearLevel = document.querySelector('select[name="yearLevel"]')?.value;
        let block = document.querySelector('select[name="block"]')?.value;
        let gender = document.querySelector('select[name="gender"]')?.value;
        let role = document.querySelector('select')?.value;

        fetch('/archiveUsers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: idNumber,
                firstname: firstname,
                lastname: lastname,
                gender: gender,
                yearLevel: yearLevel,
                block: block,
                role: role
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    });
</script>
