<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
<x-Student-components.sidebar :profile="$profile"  :firstname="$firstname" :lastname="$lastname">
    
    <div>
        <h2 class="text-3xl text-[#1a4d2e] font-bold">WELCOME, {{$firstname}} {{$lastname}}!</h2>
        <p class="text-gray-600 text-xl">DEPARTMENT {{$role}}</p>
    </div>

    <div class="grid lg:grid-cols-3 md:grid-cols-3 gap-4 mt-6">

        <div class="bg-[#1a4d2e] text-white p-6 rounded-lg shadow-lg text-center ">
            <div class="flex justify-center mt-[15px]">
                <img src="{{ asset('images/cashonhand.png') }}"
                class="w-[30%] h-[30%]" alt="Cash on Hand">
              
            <p class="font-bold text-[20px] flex place-items-center mt-2"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">AMOUNT PAID</p> 
            </div>
            <p class="text-3xl font-bold"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">    ₱{{ number_format($totalPaid, 2) }}</p>
        </div>

       <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg text-center ">
            <div class="flex justify-center mt-[15px]">
                <img src="{{ asset('images/money.png') }}"
                class="w-[30%] h-[30%]" alt="Expenses">
              
            <p class="font-bold text-[20px] flex place-items-center mt-2"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">DEPARTMENT
                EXPENSES
                </p> 
            </div>
            <p class="text-3xl font-bold"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);"> ₱{{ number_format($totalExpenses, 2) }}</p>
        </div>

        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-lg text-center ">
        <div class="flex justify-center mt-[15px]">
            <img src="{{ asset('images/receive.png') }}"
            class="w-[20%] h-[20%]" alt="Receivables">
        
        <p class="font-bold text-[20px] flex place-items-center mt-2"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">BALANCE</p> 
        </div>
        <p class="text-3xl font-bold"  style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">  ₱{{ number_format($studentBalance, 2) }}</p>
        </div>  
    </div>



    

 


</div>

</x-Student-components.sidebar>
</x-trea-components.content>
        
       
           
   
   
