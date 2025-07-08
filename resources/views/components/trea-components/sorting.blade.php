
<!-- Search and Optional Button Slot -->
<div class="flex flex-wrap md:flex-nowrap items-center justify-between mt-4 space-y-2 md:space-y-0">
    <div class="flex items-center border border-black rounded-lg p-2 w-full md:w-72">
        <input type="text" placeholder="Search..." class="w-full outline-none px-2"/>
        <button class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    {{ $slot }}
</div>

<!-- JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector("input[type='text']");
    const tbody = document.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const totalCell = document.getElementById("totalAmountCell");
    const noStudentsRow = document.getElementById("noStudentsRow"); // optional fallback row

    function updateTotalAmount() {
        const visibleRows = rows.filter(row => row.style.display !== 'none');
        let total = 0;

        visibleRows.forEach(row => {
            const rowTotal = parseFloat(row.dataset.total || "0");
            total += rowTotal;
        });

        totalCell.textContent = total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function searchTable() {
        const searchTerms = searchInput.value.toLowerCase().trim().split(/\s+/);
        let matchFound = false;

        rows.forEach(row => {
            const combinedText = row.textContent.toLowerCase();
            const isMatch = searchTerms.every(term => combinedText.includes(term));
            row.style.display = isMatch ? "" : "none";
            if (isMatch) matchFound = true;
        });

        if (noStudentsRow) {
            noStudentsRow.style.display = matchFound ? "none" : "";
        }

        updateTotalAmount(); // Update total after filtering
    }

    function sortTable(column, type = 'string', asc = true) {
        const sortedRows = rows.sort((a, b) => {
            const aVal = a.dataset[column] || "";
            const bVal = b.dataset[column] || "";

            if (type === 'date') {
                return asc
                    ? new Date(aVal) - new Date(bVal)
                    : new Date(bVal) - new Date(aVal);
            } else {
                return asc
                    ? aVal.localeCompare(bVal)
                    : bVal.localeCompare(aVal);
            }
        });

        sortedRows.forEach(row => tbody.appendChild(row));
        updateTotalAmount(); // Update total after sorting
    }

    // Search event
    searchInput.addEventListener("keyup", searchTable);

    // Sort buttons
    let isAscDate = true;
    let isAscCollectedBy = true;
    let isAscStatus = true;

    document.getElementById("sortDate").addEventListener("click", function () {
        sortTable('date', 'date', isAscDate);
        isAscDate = !isAscDate;
    });

    document.getElementById("sortCollectedBy").addEventListener("click", function () {
        sortTable('collectedby', 'string', isAscCollectedBy);
        isAscCollectedBy = !isAscCollectedBy;
    });

    document.getElementById("sortStatus").addEventListener("click", function () {
        sortTable('status', 'string', isAscStatus);
        isAscStatus = !isAscStatus;
    });
});
</script>