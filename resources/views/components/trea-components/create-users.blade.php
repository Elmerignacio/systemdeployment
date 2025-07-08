
<div id="createUserModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="flex flex-col items-center">
        <div class="bg-green-900 text-white w-full max-w-[90%] md:w-[600px] lg:w-[800px] xl:w-[600px] rounded-lg shadow-lg mt-10">


            <div class="p-6 rounded-lg w-full max-w-4xl relative"> 
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-center flex-1 ml-[30px]">CREATE USER</h3>
                    <x-trea-components.exit-btn-modal/>
                </div>
                    
                <form action="/treasurer/saveData" method="POST" id="userForm" class="space-y-4">
                    @csrf 
                    <div>
                        <label class="block mb-1 text-sm font-semibold">ID NUMBER:</label>
                        <input name="student_id" type="number" class="w-full p-2 rounded-md text-black uppercase" required id="student_id" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">FIRSTNAME:</label>
                        <input name="firstname" type="text" class="w-full p-2 rounded-md text-black uppercase" required style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">LASTNAME:</label>
                        <input name="lastname" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="lastName" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">GENDER:</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-1">
                                <input type="radio" name="gender" value="MALE" class="accent-green-500" required>
                                <span>MALE</span>
                            </label>
                            <label class="flex items-center space-x-1">
                                <input type="radio" name="gender" value="FEMALE" class="accent-green-500" required>
                                <span>FEMALE</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <div class="w-[50%]">
                            <label class="block mb-1 text-sm font-semibold">YEAR LEVEL:</label>
                            <select name="yearLevel" class="w-full p-2 rounded-md text-black uppercase" required style="text-transform: uppercase;">
                                <option value="" disabled selected>SELECT YEAR LEVEL</option>
                                <option value="1st Year">1ST YEAR</option>
                                <option value="2nd Year">2ND YEAR</option>
                                <option value="3rd Year">3RD YEAR</option>
                                <option value="4th Year">4TH YEAR</option>
                            </select>
                        </div>
                             <div class="w-[50%]">
                                <label class="block mb-1 text-sm font-semibold">BLOCK:</label>
                                <select name="block" id="block" class="w-full p-2 rounded-md text-black uppercase" required>
                                    <option value="" disabled selected>SELECT BLOCK</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                </select>
                            </div>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">USER ROLE:</label>
                        <select name="role" id="roleSelect" class="w-full p-2 rounded-md text-black uppercase" required>
                            <option value="STUDENT" selected>STUDENT</option>
                        </select>
                    </div>
                    <script>
                        document.getElementById('roleSelect').addEventListener('mousedown', function(event) {
                            event.preventDefault(); 
                        });
                    </script>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">USERNAME:</label>
                        <input name="username" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="username" readonly style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold">PASSWORD:</label>
                        <input name="password" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="password" style="text-transform: uppercase;">
                    </div>
                    <div class="text-center">
                        <button type="button" id="addUserBtn" class="bg-green-700 px-4 py-2 rounded-md hover:bg-green-600">ADD USER</button>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const searchInput = document.querySelector("input[type='text']");
                            const table = document.querySelector("table");
                            const tbody = table.querySelector("tbody");
                            const rows = Array.from(tbody.querySelectorAll("tr"));
                        
                            // Search Function
                            searchInput.addEventListener("keyup", function () {
                                const filter = searchInput.value.toLowerCase();
                                rows.forEach(row => {
                                    const description = row.children[1].textContent.toLowerCase(); 
                                    if (description.includes(filter)) {
                                        row.style.display = "";
                                    } else {
                                        row.style.display = "none";
                                    }
                                });
                            });
                        

                            document.querySelectorAll("th").forEach((header, columnIndex) => {
                                header.addEventListener("click", function () {
                                    const isNumeric = columnIndex > 1 && columnIndex < 4; 
                                    const direction = header.dataset.order === "asc" ? "desc" : "asc";
                                    header.dataset.order = direction;
                        
                                    const sortedRows = rows.sort((a, b) => {
                                        let valA = a.children[columnIndex].textContent.trim();
                                        let valB = b.children[columnIndex].textContent.trim();
                        
                                        if (isNumeric) {
                                            valA = parseFloat(valA.replace(/[₱,]/g, "")) || 0;
                                            valB = parseFloat(valB.replace(/[₱,]/g, "")) || 0;
                                        }
                        
                                        return direction === "asc" ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
                                    });
                        
                                    tbody.innerHTML = "";
                                    sortedRows.forEach(row => tbody.appendChild(row));
                                });
                            });
                        });
                    </script>
    
                    <script>
                        document.getElementById("student_id").addEventListener("input", function() {
                            document.getElementById("username").value = this.value; 
                        });
                    
                        document.getElementById("lastName").addEventListener("input", function() {
                            document.getElementById("password").value = this.value; 
                        });
                    </script>

                 <x-trea-components.adduser-modal/>

              
                </form>
            </div>
        </div>
    </div>
</div>



    
