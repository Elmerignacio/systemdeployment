<header class="flex items-center justify-between lg:justify-center bg-[url({{ asset('images/header.png') }})]
bg-cover bg-center text-white p-4 font-bold text-2xl">
   <div class="flex items-center">
       <img class="w-[60px] h-[60px] rounded-full mr-4" 
           src="{{ asset('images/gccLogo.png') }}"
           alt="Logo">
       <span class="block lg:hidden whitespace-nowrap">CIT-FMS</span>
   </div>
   <span class="hidden lg:block whitespace-nowrap">COLLEGE OF INFORMATION TECHNOLOGY FUND MANAGEMENT SYSTEM</span>
   <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden">
       <i class="fas fa-bars text-3xl"></i>
   </button>
</header>
