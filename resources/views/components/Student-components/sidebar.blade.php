<div class="flex h-screen overflow-auto md:overflow-hidden">
  <aside 
    v-show="window.innerWidth >= 1024 || sidebarOpen"
    @mouseover="if (window.innerWidth >= 1024) sidebarOpen = true" 
    @mouseleave="if (window.innerWidth >= 1024) sidebarOpen = false"
    :class="{
        'w-60 translate-x-0': sidebarOpen, 
        'w-0 overflow-hidden': !sidebarOpen, 
        'lg:w-64': sidebarOpen, 
        'lg:w-20': !sidebarOpen
    }"
    class="fixed lg:relative h-full bg-green-800 shadow-lg transform transition-all duration-300 ease-in-out lg:translate-x-0 z-50">
    
    <nav class="mt-10 ml-4 space-y-2 flex flex-col gap-4 justify-center text-[15px] text-bolds pr-5">
        
        <a href="/student/userDetails" class="flex items-center text-white rounded-md mb-5">
            <input type="image"   src="{{ asset('storage/' . ($profile ? $profile->profile : 'images/1.jpg')) }}"
            alt="Profile" class="rounded-full w-10 h-10 border-2 border-white">

            <span 
            :class="sidebarOpen ? 'inline-block ml-4 whitespace-nowrap overflow-hidden text-ellipsis max-w-[150px]' : 'hidden'"
            class="text-white text-[12px] font-bold transition-all duration-300"
            id="userFullName"
            title="{{ $firstname }} {{ $lastname }}"
        >
            {{ $firstname }} {{ $lastname }}
        </span>
        </a>
        
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                fetch('/get-user-info')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('userFullName').textContent = data.firstname + " " + data.lastname;
                    })
                    .catch(error => console.error('Error fetching user info:', error));
            });
        </script>
        
        <a href="/student/dashboard" class="flex items-center p-2 text-white hover:bg-green-500 rounded-md">
            <i class="fas fa-home text-2xl flex-shrink-0"></i>
            <span :class="{'inline-block ml-4 whitespace-nowrap overflow-hidden': sidebarOpen, 'hidden': !sidebarOpen}" class="text-white transition-all duration-300">
                DASHBOARD
            </span>
        </a>
        <a href="/student/ledgers" class="flex items-center p-2 text-white hover:bg-green-500 rounded-md">
            <i class="fas fa-user-graduate text-2xl flex-shrink-0"></i>
            <span :class="{'inline-block ml-4 whitespace-nowrap overflow-hidden': sidebarOpen, 'hidden': !sidebarOpen}" class="text-white transition-all duration-300">
                MY BALANCE
            </span>
        </a>
        <a href="/student/payableManagement" class="flex items-center p-2 text-white hover:bg-green-500 rounded-md">
            <i class="fas fa-file-invoice-dollar text-2xl flex-shrink-0"></i>
            <span :class="{'inline-block ml-4 whitespace-nowrap overflow-hidden': sidebarOpen, 'hidden': !sidebarOpen}" class="text-white transition-all duration-300">
                PAYABLE MANAGEMENT
            </span>
        </a>
        <a href="/student/expense" class="flex items-center p-1 text-white hover:bg-green-500 rounded-md">
            <i class="fas fa-money-check-alt text-2xl flex-shrink-0"></i>
            <span :class="{'inline-block ml-4 whitespace-nowrap overflow-hidden': sidebarOpen, 'hidden': !sidebarOpen}" class="text-white transition-all duration-300">
                EXPENSES
            </span>
        </a>
        <a href="{{ route('logout') }}" class="flex items-center p-2 text-white hover:bg-red-600 rounded-md"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
         <i class="fas fa-sign-out-alt text-2xl flex-shrink-0"></i>
         <span :class="{'inline-block ml-4 whitespace-nowrap overflow-hidden': sidebarOpen, 'hidden': !sidebarOpen}" class="text-white transition-all duration-300">
             Logout
         </span>
     </a>
     <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</aside>

<div class="flex flex-col p-4 sm:p-10 text-left w-full">
{{$slot}}
</div>

</div>