<!-- Main Update Payable Modal -->
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
        class="absolute top-2 right-2 text-green-700 hover:text-red-500 p-4"
        @click="showDetails = false"
    >
        <i class="fas fa-times text-lg"></i>
    </button>

    <h2 class="text-lg font-bold text-center mb-4 text-green-700">UPDATE PAYABLE</h2>

    <div class="space-y-3">
        <div>
            <label class="block text-sm">DESCRIPTION:</label>
            <input type="text" x-model="selectedPayable.description" class="w-full p-2 rounded bg-white text-black border-2 border-green-700" />
        </div>
        <div>
            <label class="block text-sm">AMOUNT:</label>
            <input type="text" x-model="selectedPayable.amount" class="w-full p-2 rounded bg-white text-black border-2 border-green-700" />
        </div>
        <div>
            <label class="block text-sm">DUE DATE:</label>
            <input type="date" x-model="selectedPayable.dueDate" class="w-full p-2 rounded bg-white text-black border-2 border-green-700">
        </div>
    </div>

    <div class="flex justify-center gap-5 mt-4">
        <button 
            type="button"
            class="showDeleteModalBtn bg-red-600 text-white px-3 py-1 rounded"
            x-data
            @click="
                const deleteUrl = '/payable/delete/' + encodeURIComponent(selectedPayable.description);
                $refs.dynamicDeleteForm.action = deleteUrl;
                document.getElementById('deleteModal').classList.remove('hidden');">
            Delete
        </button>
        <x-trea-components.delete-modal/>


     

        <!-- Update Button -->
        <form x-ref="updateForm" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="description" x-bind:value="selectedPayable.description">
            <input type="hidden" name="amount" x-bind:value="selectedPayable.amount">
            <input type="hidden" name="dueDate" x-bind:value="selectedPayable.dueDate">
            <button 
                type="submit" 
                class="bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded"
                @click.prevent="
                    const encodedDescription = encodeURIComponent(selectedPayable.description);
                    $refs.updateForm.action = '/payable/update/' + encodedDescription;
                    $refs.updateForm.submit();
                "
            >
                MODIFY
            </button>
        </form>
    </div>
</div>
