@props(['profile', 'firstname' , 'lastname'])

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
    class="fixed lg:relative h-full bg-green-800 shadow-xl transition-all duration-300 ease-in-out lg:translate-x-0 z-50">

    <nav class="mt-10 px-4 space-y-4 text-[15px] font-semibold text-white">

        <!-- Profile Section -->
    <a href="/treasurer/userDetails" class="flex items-center gap-4 p-2 hover:bg-green-700 rounded-lg transition-all">
        <input
            type="image"
            src="{{ asset('storage/' . ($profile ? $profile->profile : 'images/1.jpg')) }}"
            alt="Profile"
            class="rounded-full w-12 h-12 border-2 border-white object-cover"
        />
        <div :class="sidebarOpen ? 'block' : 'hidden'" class="transition-all max-w-[180px]">
            <div id="userFullName"
                class=" font-semibold text-white leading-tight break-words"
                title="{{ $firstname }} {{ $lastname }}">
                {{ $firstname }} {{ $lastname }}
            </div>
            <div class="text-xs text-green-200 mt-1 font-medium">DEPT. {{ session('role') }}</div>
        </div>
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


        <!-- Navigation Links -->
        @php
            $navItems = [
                ['href' => '/treasurer/dashboard', 'icon' => 'fas fa-home', 'label' => 'DASHBOARD'],
                ['href' => '/treasurer/studentBalance', 'icon' => 'fas fa-user-graduate', 'label' => 'STUDENT BALANCES'],
                ['href' => '/treasurer/collection', 'icon' => 'fas fa-wallet', 'label' => 'COLLECTIONS'],
                ['href' => '/treasurer/payableManagement', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'PAYABLE MANAGEMENT'],
                ['href' => '/treasurer/expense', 'icon' => 'fas fa-money-check-alt', 'label' => 'EXPENSES'],
                ['href' => '/treasurer/report', 'icon' => 'fas fa-chart-pie', 'label' => 'REPORTS'],
                ['href' => '/treasurer/manageUser', 'icon' => 'fas fa-users-cog', 'label' => 'USER MANAGEMENT'],
            ];
        @endphp

        @foreach($navItems as $item)
        <a href="{{ $item['href'] }}" class="flex items-center gap-4 p-2 rounded-lg hover:bg-green-600 transition-all">
            <i class="{{ $item['icon'] }} text-2xl"></i>
            <span :class="{ 'inline-block': sidebarOpen, 'hidden': !sidebarOpen }" class="transition-all truncate">
                {{ $item['label'] }}
            </span>
        </a>
        @endforeach

        <!-- Logout -->
        <a href="{{ route('logout') }}" class="flex items-center gap-4 p-2 rounded-lg hover:bg-red-600 transition-all"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt text-2xl"></i>
            <span :class="{ 'inline-block': sidebarOpen, 'hidden': !sidebarOpen }" class="transition-all truncate">
                Logout
            </span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="flex flex-col p-4 sm:p-10 w-full">
      {{ $slot }}
  </div>
</div>
