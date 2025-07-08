<!-- Add this to include Heroicons for the eye icon -->


<x-trea-components.login />

<h1 class="text-2xl font-bold text-[#1a4d2e] ">
    College of Information Technology<br>
    Fund Management System
</h1>
@if($errors->has('loginError'))
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Login Failed:</strong>
        <span class="block sm:inline">{{ $errors->first('loginError') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mt-[5%] bg-[#1a4d2e] p-8 sm:p-10 md:p-12 rounded-lg">
        <div class="mb-6 relative">
            <label class="block text-left text-white px-1 font-bold" for="username">Username</label>
            <input class="w-full px-6 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-400" 
                   type="text" id="username" name="username" placeholder="Username" required>
        </div>

        <div class="mb-6 relative">
            <label class="block text-left text-white px-1 font-bold" for="password">Password</label>
            <input class="w-full px-6 py-3 pr-12 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-400" 
                   type="password" id="password" name="password" placeholder="Password" required>

            <!-- Eye icon -->
            <button type="button" onclick="togglePassword()" class="absolute right-4 top-[65%] transform -translate-y-1/2 text-[#1a4d2e]">
                <i data-feather="eye" id="eyeIcon"></i>
            </button>
        </div>

        <button type="submit" class="w-[100px] text-1xl bg-[#1a4d2e] shadow-lg text-white font-bold px-4 py-2 rounded-[20px] border-2 hover:bg-white hover:text-[#1a4d2e] transition duration-300">
            Login
        </button>
    </div>
</form>
<script src="https://unpkg.com/feather-icons"></script>



<!-- JavaScript for toggling password visibility -->
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-feather', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-feather', 'eye');
        }
        feather.replace(); // re-render icon
    }

    // Render Feather icons on load
    window.onload = () => feather.replace();
</script>
<script>
    setTimeout(() => {
        const alertBox = document.querySelector('[role="alert"]');
        if (alertBox) alertBox.style.display = 'none';
    }, 3000);
</script>
