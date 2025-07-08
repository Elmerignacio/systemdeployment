
<div 
x-show="showDetails"
x-transition:enter="transition duration-300 transform"
x-transition:enter-start="-translate-y-10 opacity-0"
x-transition:enter-end="translate-y-0 opacity-100"
x-transition:leave="transition duration-200 transform"
x-transition:leave-start="translate-y-0 opacity-100"
x-transition:leave-end="-translate-y-10 opacity-0"
class="h-[100%] w-full md:w-1/2 mx-auto p-9 mt-4 bg-gray-300 bg-opacity-40 shadow-lg border-2 border-green-700 rounded-lg relative"
>
<button 
    class="absolute top-2 right-2 text-green-600 hover:text-red-500 p-4"
    @click="showDetails = false"
>
    <i class="fas fa-times text-lg"></i>
</button>
<h2 class="text-lg font-bold text-center mb-4 text-green-700" >UPDATE PAYABLE</h2>
<div class="space-y-3">
    <div>
        <label class="block text-sm">DESCRIPTION:</label>
        <input type="text" x-model="selectedPayable.description" class="w-full p-2 rounded bg-white text-black border-2 border-green-700 readonly/>
    </div>
    <div>
        <label class="block text-sm">AMOUNT:</label>
        <input type="text" x-model="selectedPayable.amount" class="w-full p-2 rounded bg-white text-black border-2 border-green-700 readonly/>
    </div>
    <div>
        <label class="block text-sm">DUE DATE:</label>
        <input type="date" x-model="selectedPayable.dueDate" class="w-full p-2 rounded bg-white text-black  border-2 border-green-700 readonly>
    </div>
</div>
<div class="flex justify-center gap-5 mt-4">
</div>
</div>
</div>

<script>
   document.addEventListener("DOMContentLoaded", function () {
       const editButtons = document.querySelectorAll(".edit-btn");
       const modal = document.getElementById("editModal");
       const closeButton = document.getElementById("closeModal");
   
       // Input fields sa modal
       const descInput = modal.querySelector("input[x-model='selectedPayable.description']");
       const amountInput = modal.querySelector("input[x-model='selectedPayable.amount']");
       const dueDateInput = modal.querySelector("input[x-model='selectedPayable.dueDate']");
       const yearLevelInput = modal.querySelector("input[x-model='selectedPayable.yearLevel']");
       const blockInput = modal.querySelector("input[x-model='selectedPayable.block']");
   
       editButtons.forEach(button => {
           button.addEventListener("click", function () {
               const row = this.closest("tr"); 
   
               descInput.value = row.dataset.description;
               amountInput.value = row.dataset.amount;
               dueDateInput.value = new Date(row.dataset.dueDate).toISOString().split("T")[0];
               yearLevelInput.value = row.dataset.yearLevel;
               blockInput.value = row.dataset.block;
   
               modal.classList.remove("hidden"); // Show modal
           });
       });
   
       closeButton.addEventListener("click", function () {
           modal.classList.add("hidden");
       });
   
       modal.addEventListener("click", function (event) {
           if (event.target === modal) {
               modal.classList.add("hidden");
           }
       });
   });
   </script>
   
