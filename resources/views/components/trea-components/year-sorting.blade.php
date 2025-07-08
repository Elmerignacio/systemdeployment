<div class="flex items-center space-x-2 mt-5">
    <select id="schoolYearFilter" class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
        <option value="" selected>All School Years</option>
        <option value="2024-2025">2024-2025</option>
        <option value="2023-2024">2023-2024</option>
        <option value="2022-2023">2022-2023</option>
    </select>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const schoolYearFilter = document.getElementById("schoolYearFilter");
        const rows = document.querySelectorAll("tbody tr");
    
        schoolYearFilter.addEventListener("change", function () {
            const selectedYear = this.value;
            
            rows.forEach(row => {
                const dueDate = row.children[4].textContent.trim(); 
    
                if (selectedYear === "" || dueDate.includes(selectedYear)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
    </script>