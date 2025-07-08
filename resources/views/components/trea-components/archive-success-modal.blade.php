
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
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
            <button id="continueBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">CONTINUE</button>
        </div>
    </div>
    </div>
</div>

<style>
    @keyframes checkmark {
        0% { opacity: 0; transform: scale(0.5); }
        100% { opacity: 1; transform: scale(1); }
    }

    .checkmark-animate {
        animation: checkmark 0.3s ease-out forwards;
    }
</style>