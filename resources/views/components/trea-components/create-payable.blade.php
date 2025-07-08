@php
    $user = Auth::check() ? Auth::user() : null;
@endphp

<!-- CREATE PAYABLE MODAL -->
<div id="createUserModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="flex flex-col items-center">
        <div class="bg-green-900 text-white w-full max-w-[90%] md:w-[600px] lg:w-[800px] xl:w-[600px] rounded-lg shadow-lg mt-10">
            <div class="p-6 rounded-lg w-full max-w-4xl relative"> 
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-center flex-1 ml-[30px]">CREATE PAYABLE</h3>
                    <x-trea-components.exit-btn-modal/>
                </div>

                <form id="payableForm" action="savePayable" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-1 text-sm font-semibold">DESCRIPTION:</label>
                        <input type="text" name="description" class="w-full p-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" required style="text-transform: uppercase;">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-semibold">AMOUNT:</label>
                        <input type="number" name="amount" step="0.01" class="w-full p-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-semibold">DUE DATE:</label>
                        <input type="date" name="dueDate" class="w-full p-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>

                    {{$slot}}

                    <div>
                        <label class="block mb-1 text-sm font-semibold">BLOCK:</label>
                        <select id="block" name="block" class="w-full p-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">SELECT BLOCK</option>
                            <option value="all">ALL BLOCK</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-semibold">STUDENT:</label>
                        <select id="student" name="student_id" class="w-full p-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            @if($user && ($user->role === 'student' || $user->role === 'representative' || $user->role === 'treasurer'))
                                <option value="{{ $user->student_id }}">{{ strtoupper($user->firstname) }} {{ strtoupper($user->lastname) }}</option>
                            @else
                                <option value="">SELECT STUDENT</option>
                                <option value="all">ALL STUDENTS</option>
                            @endif
                        </select>
                    </div>

                    <div class="text-center mt-5">
                        <button type="button" id="addPayableBtn" class="bg-green-700 px-4 py-2 rounded-md hover:bg-green-600 text-white font-bold">
                            ADD PAYABLE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<x-trea-components.addpayable-modal />

<!-- ✅ SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const archiveModal = document.getElementById("Add"); // Confirmation Modal
    const successModal = document.getElementById("Confirmation"); // Success Modal
    const confirmButton = archiveModal.querySelector(".confirmBtn");
    const cancelButton = archiveModal.querySelector(".cancelBtn");
    const successConfirmButton = successModal.querySelector("button[type='submit']");
    const addPayableBtn = document.getElementById("addPayableBtn");
    const form = document.getElementById("payableForm");

    addPayableBtn.addEventListener("click", function () {
        if (form.checkValidity()) {
            archiveModal.classList.remove("hidden");
        } else {
            form.reportValidity(); // shows HTML5 validation message
        }
    });

    confirmButton.addEventListener("click", function () {
        archiveModal.classList.add("hidden");
        successModal.classList.remove("hidden");
    });

    cancelButton.addEventListener("click", function () {
        archiveModal.classList.add("hidden");
    });

    successConfirmButton.addEventListener("click", function () {
        successModal.classList.add("hidden");
        form.submit();
    });

    // Fetch students and blocks logic
    const yearLevelDropdown = document.getElementById("yearLevel");
    const blockDropdown = document.getElementById("block");
    const studentDropdown = document.getElementById("student");

    @if(!$user || !in_array($user->role, ['student', 'representative', 'treasurer']))
    if (yearLevelDropdown) {
        yearLevelDropdown.addEventListener("change", function () {
            const yearLevel = this.value;
            if (yearLevel) {
                fetch(`/treasurer/get-students-and-blocks?yearLevel=${yearLevel}`)
                    .then(response => response.json())
                    .then(data => {
                        blockDropdown.innerHTML = '<option value="all">ALL BLOCK</option>';
                        data.blocks.forEach(block => {
                            blockDropdown.innerHTML += `<option value="${block.block}">${block.block.toUpperCase()}</option>`;
                        });
                        blockDropdown.dataset.students = JSON.stringify(data.students);

                        studentDropdown.innerHTML = '<option value="all">ALL STUDENTS</option>';
                    })
                    .catch(error => console.error("ERROR FETCHING DATA:", error));
            } else {
                blockDropdown.innerHTML = '<option value="all">ALL BLOCK</option>';
                studentDropdown.innerHTML = '<option value="all">ALL STUDENTS</option>';
            }
        });
    }

    if (blockDropdown && studentDropdown) {
        blockDropdown.addEventListener("change", function () {
            const selectedBlock = this.value;
            const allStudents = JSON.parse(blockDropdown.dataset.students || "[]");

            studentDropdown.innerHTML = '<option value="all">ALL STUDENTS</option>';

            const filteredStudents = selectedBlock === "all"
                ? allStudents
                : allStudents.filter(student => student.block === selectedBlock);

            filteredStudents.forEach(student => {
                studentDropdown.innerHTML += `<option value="${student.student_id}">${student.firstname.toUpperCase()} ${student.lastname.toUpperCase()}</option>`;
            });
        });
    }
    @endif
});
</script>
