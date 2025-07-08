
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
        class="absolute top-2 right-2 text-[#1a4d2e] hover:text-red-500 p-4"
        @click="showDetails = false"
    >
        <i class="fas fa-times text-lg"></i>
    </button>

    <div class="relative">
  
        <form action="{{ route('AdModify.users') }}" method="POST">
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
                        class="w-full p-2 border-2 border-[#1a4d2e] text-black rounded-md focus:ring-0 focus:outline-none"
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
                    type="submit" 
                    name="action" 
                    value="archive" 
                    class="bg-red-600 px-4 py-2 rounded-md text-white font-bold w-full md:w-auto"
                >
                    ARCHIVE
                </button>
        
                <button 
                    type="submit" 
                    name="action" 
                    value="modify" 
                    class="bg-[#1a4d2e] px-4 py-2 rounded-md text-white font-bold w-full md:w-auto"
                >
                    MODIFY
                </button>
            </div>
        </form>
        
            {{-- @csrf
            <div id="archiveMessage" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96 h-[40%] border-2 border-green-700 flex flex-col justify-center">
                    <div class="flex flex-col items-center">
                        <img class="w-[38%] h-[100%] mb-4 " src="https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.15752-9/484109607_1006401184709585_8887677381926160098_n.png?stp=cp0_dst-png&_nc_cat=109&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGZF4_VnJnVCdsq9CD0ZbKfPW9wfnHcCyY9b3B-cdwLJpXEWj0ZPW1hNSMpN3-wiOXZOQD86vxuqvdrh3e-Leo_&_nc_ohc=Y3xgaJJo7w4Q7kNvgFewM7B&_nc_oc=Adj_cHRFUystvAUYcEL73NefrMXG_sHtgZSxCkdH2FenOet5fjX5p_p5XDKHClo3liO96zsyi-2Ev5T2YgYym4K5&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&oh=03_Q7cD1wHENZrVtHQbnJWIOkKsqr1i_djc1gct77mVH-wWB3ZoMg&oe=67FEFA89" 
                        alt="Archive Box" class="w-16 h-16 mb-4">
                        <p class="text-red-600 text-center font-semibold">Are you sure you want to archive this item?</p>
                        <p class="text-gray-600 text-sm text-center mt-2">
                            Once archived, it will be moved to the archive list and will no longer be actively visible.
                        </p>
                        <div class="flex mt-4 space-x-4">
                            <button type="submit" id="CancelButton" class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition">CANCEL</button>
                      
                        </button>
                            <button type="submit" id="archiveButton" class="bg-green-700 px-4 py-2 rounded-md text-white font-bold w-full md:w-auto">PROCEED</button>
                        </div>
                    </div>
                </div>
            </div>
            

            @csrf
            <div id="successMessage" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                <div class="relative bg-white p-6 rounded-lg shadow-lg w-96 h-[40%] border-2 border-green-700 flex flex-col justify-center">
                <div class="flex flex-col items-center mt-[8px]">
                    <img class="w-[38%] h-auto mb-4" src="https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.15752-9/484109607_1006401184709585_8887677381926160098_n.png?stp=cp0_dst-png&_nc_cat=109&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGZF4_VnJnVCdsq9CD0ZbKfPW9wfnHcCyY9b3B-cdwLJpXEWj0ZPW1hNSMpN3-wiOXZOQD86vxuqvdrh3e-Leo_&_nc_ohc=h2ARRaZj2PEQ7kNvgEHOkPu&_nc_oc=Adh-umMbzQ9XH9Ld7sjmZIckoPFezyPocTm0WHQQmLMObt6vZHWRO21WJ-w55FyCLuvc7v4YUDbqvvvBk55dpA_9&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&oh=03_Q7cD1wEs8VOb4X6FD2uoSvhPjXJQSawfSoBCXDWdpW_IjofEMQ&oe=68004C09   "
                     alt="Archive Box" class="w-16 h-16 mb-4">
                    
                    <!-- Checkmark SVG -->
                    <div class="absolute mt-[77px]  transform -translate-x-1/2 checkmark-animate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-20 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
            
                    <p class="text-green-600 text-center font-semibold">Item successfully archived. It is no longer actively visible and has been moved to the archive list.</p>   
                    <div class="flex mt-6 space-x-4">
                        <button id="continueBtn" class="bg-green-700 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">CONTINUE</button>
                    </div>
                </div>
            </div>
            </div>
     

</div>
    </div> --}}

      
        <script>
    
            document.getElementById('archiveBTN').addEventListener('click', function(event) {
            event.preventDefault();
            let archiveMessage = document.getElementById('archiveMessage');
            archiveMessage.classList.remove('hidden');   
        });
        
        document.getElementById('CancelButton').addEventListener('click', function(event) {
            event.preventDefault();
            let successMessage = document.getElementById('successMessage');
            successMessage.classList.add('hidden');
            archiveMessage.classList.add('hidden');
        });
        
        document.getElementById('archiveButton').addEventListener('click', function(event) {
            event.preventDefault();
            let successMessage = document.getElementById('successMessage');
            successMessage.classList.remove('hidden');
            archiveMessage.classList.add('hidden');
        });
        
        </script>
        <script>
            document.getElementById('archiveBTN').addEventListener('click', function() {
                let idNumber = document.querySelector('input[value="ID"]').value;
                let firstname = document.querySelector('input[value="firstname"]').value;
                let lastname = document.querySelector('input[value="lastname"]').value;
                let yearLevel = document.querySelector('input[value="yearLevel"]').value;
                let block = document.querySelector('input[value="block"]').value;
                let gender = document.querySelector('input[name="gender"]:checked').nextElementSibling.innerText;
                let role = document.querySelector('select').value;
            
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



