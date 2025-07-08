<form action="{{ route('archive.users') }}" method="POST" id="archiveForm">
    @csrf

    <div id="archiveModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden z-100">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 h-[40%] border-2 border-green-700 flex flex-col justify-center">
            <div class="flex flex-col items-center">
                <img class="w-[38%] h-[100%] mb-4 " src="https://scontent.fmnl13-4.fna.fbcdn.net/v/t1.15752-9/484109607_1006401184709585_8887677381926160098_n.png?stp=cp0_dst-png&_nc_cat=109&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGZF4_VnJnVCdsq9CD0ZbKfPW9wfnHcCyY9b3B-cdwLJpXEWj0ZPW1hNSMpN3-wiOXZOQD86vxuqvdrh3e-Leo_&_nc_ohc=Y3xgaJJo7w4Q7kNvgFewM7B&_nc_oc=Adj_cHRFUystvAUYcEL73NefrMXG_sHtgZSxCkdH2FenOet5fjX5p_p5XDKHClo3liO96zsyi-2Ev5T2YgYym4K5&_nc_zt=23&_nc_ht=scontent.fmnl13-4.fna&oh=03_Q7cD1wHENZrVtHQbnJWIOkKsqr1i_djc1gct77mVH-wWB3ZoMg&oe=67FEFA89" 
                alt="Archive Box" class="w-16 h-16 mb-4">
                <p class="text-red-600 text-center font-semibold">Are you sure you want to archive this item?</p>
                <p class="text-gray-600 text-sm text-center mt-2">
                    Once archived, it will be moved to the archive list and will no longer be actively visible.
                </p>
                <div class="flex mt-4 space-x-4">
                    <button id="cancelBtn" type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition">
                        CANCEL
                    </button>
                    <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-700">
                        PROCEED
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const archiveButtons = document.querySelectorAll("#archiveBTN");

    archiveButtons.forEach(button => {
        button.addEventListener("click", function () {
            const row = this.closest("tr");
            const userId = row.children[1].textContent.trim(); 

            document.getElementById("archiveUserId").value = userId;

            // Show archive modal
            document.getElementById("archiveModal").style.display = "flex";
        });
    });

    // Close modal functionality
    document.getElementById("cancelBtn").addEventListener("click", function () {
        document.getElementById("archiveModal").style.display = "none";
    });
});

</script>
<script>
    document.getElementById("archiveBtn").addEventListener("click", function () {
        document.getElementById("archiveModal").classList.remove("hidden");
    });

    document.getElementById("cancelBtn").addEventListener("click", function () {
        document.getElementById("archiveModal").classList.add("hidden");
    });
</script>

 <!--script for disable button when no student selected--->  
 <script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll(".rowCheckbox");
        const selectAll = document.getElementById("selectAll");
        const archiveBtn = document.getElementById("archiveBtn");

        function updateButtonState() {
            const anyChecked = [...checkboxes].some(checkbox => checkbox.checked);
            archiveBtn.disabled = !anyChecked;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", updateButtonState);
        });

        selectAll.addEventListener("change", function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateButtonState();
        });

        function toggleCheckbox(event, row) {
            if (!event.target.classList.contains("rowCheckbox")) {
                const checkbox = row.querySelector(".rowCheckbox");
                checkbox.checked = !checkbox.checked;
                updateButtonState();
            }
        }

        window.toggleCheckbox = toggleCheckbox; // Ensure function is accessible globally
    });
</script>
 
<!--script for checkbox--->  
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const archiveForm = document.getElementById("archiveForm");
        const archiveModal = document.getElementById("archiveModal");
        const successModal = document.getElementById("successModal");
        const cancelBtn = document.getElementById("cancelBtn");
        const proceedBtn = archiveForm.querySelector("button[type='submit']");
        const continueBtn = document.getElementById("continueBtn");

        function showArchiveModal() {
            archiveModal.classList.remove("hidden");
        }

        cancelBtn.addEventListener("click", function () {
            archiveModal.classList.add("hidden");
        });

        proceedBtn.addEventListener("click", function (event) {
            event.preventDefault();
            archiveModal.classList.add("hidden");
            successModal.classList.remove("hidden");
        });

        continueBtn.addEventListener("click", function () {
            successModal.classList.add("hidden"); 
            archiveForm.submit();
        });
    });
function toggleCheckbox(event, row) {
    if (event.target.closest('.notClickable') || event.target.type === 'checkbox') {
        return;
    }
    let checkbox = row.querySelector('.rowCheckbox');
    checkbox.checked = !checkbox.checked;
}

document.getElementById('selectAll').addEventListener('change', function () {
    let checkboxes = document.querySelectorAll('.rowCheckbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});
document.getElementById('archiveBtn').addEventListener('click', function (event) {
    event.preventDefault();

    let selectedStudents = [];
    document.querySelectorAll('.rowCheckbox:checked').forEach(checkbox => {
        selectedStudents.push(checkbox.value);
    });

    if (selectedStudents.length === 0) {
        alert("Please select at least one student to archive.");
        return;
    }

    const archiveForm = document.getElementById('archiveForm');
    document.querySelectorAll('.studentInput').forEach(input => input.remove());

    selectedStudents.forEach(studentId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'students[]';
        input.value = studentId;
        input.classList.add('studentInput');
        archiveForm.appendChild(input);
    });

    document.getElementById('archiveModal').classList.remove('hidden');
});

document.getElementById('cancelBtn').addEventListener('click', function () {
    document.getElementById('archiveModal').classList.add('hidden');
});

document.getElementById('proceedBtn').addEventListener('click', function () {
    document.getElementById('archiveModal').classList.add('hidden');
    document.getElementById('archiveForm').submit();
    setTimeout(() => {
        document.getElementById('successModal').classList.remove('hidden');
    }, 500);
});

document.getElementById('continueBtn').addEventListener('click', function () {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
});

document.querySelectorAll('#usersTableBody tr').forEach(row => {
    row.addEventListener('click', function (event) {
        toggleCheckbox(event, this);
    });
});
</script>
