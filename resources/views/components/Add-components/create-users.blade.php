
<div id="createUserModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="flex flex-col items-center">
        <div class="bg-green-900 text-white w-full max-w-[90%] md:w-[600px] lg:w-[800px] xl:w-[600px] rounded-lg shadow-lg mt-10">


            <div class="p-6 rounded-lg w-full max-w-4xl relative"> 
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-center flex-1 ml-[30px]">CREATE USER</h3>
                    <x-trea-components.exit-btn-modal/>
                </div>
                    
                <form action="/admin/saveData" method="POST" id="userForm" class="space-y-4">
    @csrf 
    <div>
        <label class="block mb-1 text-sm font-semibold">ID NUMBER:</label>
        <input name="student_id" type="number" class="w-full p-2 rounded-md text-black uppercase" required id="student_id">
    </div>

    <div>
        <label class="block mb-1 text-sm font-semibold">FIRSTNAME:</label>
        <input name="firstname" type="text" class="w-full p-2 rounded-md text-black uppercase" required>
    </div>

    <div>
        <label class="block mb-1 text-sm font-semibold">LASTNAME:</label>
        <input name="lastname" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="lastName">
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
            <select name="yearLevel" id="yearLevel" class="w-full p-2 rounded-md text-black uppercase" required>
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
            <option value="" disabled selected>-- SELECT ROLE --</option>
            <option value="ADMIN">ADMIN</option>
            <option value="TREASURER">TREASURER</option>
            <option value="REPRESENTATIVE">REPRESENTATIVE</option>
            <option value="STUDENT">STUDENT</option>
        </select>
    </div>

    <div>
        <label class="block mb-1 text-sm font-semibold">USERNAME:</label>
        <input name="username" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="username" readonly>
    </div>

    <div>
        <label class="block mb-1 text-sm font-semibold">PASSWORD:</label>
        <input name="password" type="text" class="w-full p-2 rounded-md text-black uppercase" required id="password">
    </div>

    <div class="text-center">
        <button type="button" id="addUserBtn" class="bg-green-700 px-4 py-2 rounded-md hover:bg-green-600">ADD USER</button>
    </div>


    <style>
        select:disabled,
        input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    <script>
        document.getElementById("student_id").addEventListener("input", function () {
            document.getElementById("username").value = this.value;
        });

        document.getElementById("lastName").addEventListener("input", function () {
            document.getElementById("password").value = this.value;
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const roleSelect = document.getElementById("roleSelect");
            const yearLevel = document.getElementById("yearLevel");
            const block = document.getElementById("block");

            function toggleFields() {
                const isAdmin = roleSelect.value === "ADMIN";
                yearLevel.disabled = isAdmin;
                block.disabled = isAdmin;

                if (isAdmin) {
                    yearLevel.value = "";
                    block.value = "";
                }
            }

            toggleFields();
            roleSelect.addEventListener("change", toggleFields);
        });
    </script>

    <x-trea-components.adduser-modal />
</form>

            </div>
        </div>
    </div>
</div>



    
