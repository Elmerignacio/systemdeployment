<x-trea-components.layout/>
<x-trea-components.header/>
<x-trea-components.content>
    <x-Repre-components.sidebar :profile="$profile" :firstname="$firstname" :lastname="$lastname">

        <div class="flex mt-[100px] justify-center h-screen px-4">
            <div class="flex flex-col items-center w-full max-w-md md:max-w-lg lg:max-w-xl">
                <h1 class="text-2xl mb-[70px] text-green-600"><strong>USER ACCOUNT DETAILS</strong></h1>
                <div class="relative w-full bg-green-900 text-white p-12 rounded-xl shadow-2xl">
                    <a href="/representative/dashboard">
                        <button class="absolute top-2 right-6 text-white text-3xl hover:text-red-500">&times;</button>
                    </a>

                    <div class="relative flex justify-center -mt-24">
                        <img id="previewImage" class="w-32 h-32 rounded-full border-4 border-white"
                            src="{{ asset('storage/' . ($profile ? $profile->profile : 'images/1.jpg')) }}"
                            alt="User Profile">
                        <label for="profileImage"
                            class="absolute bottom-1 right-2 bg-gray-800 text-white p-2 rounded-full cursor-pointer">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>

                    <div class="text-center mt-8">
                    @if (session('success'))
                    <div id="successModalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300">
                        <div id="successModal" class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full transform scale-90 opacity-0 transition duration-300">
                            <div class="text-green-600 font-semibold text-lg text-center">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (session('error'))
                    <div id="errorModalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 pointer-events-none transition-opacity duration-300">
                        <div id="errorModal" class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full transform scale-90 opacity-0 transition duration-300">
                            <div class="text-red-600 font-semibold text-lg text-center">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const showModal = (backdropId, modalId) => {
                            const backdrop = document.getElementById(backdropId);
                            const modal = document.getElementById(modalId);

                            if (backdrop && modal) {
                                setTimeout(() => {
                                    backdrop.classList.remove('opacity-0', 'pointer-events-none');
                                    backdrop.classList.add('opacity-100');
                                    modal.classList.remove('opacity-0', 'scale-90');
                                    modal.classList.add('opacity-100', 'scale-100');
                                }, 10);

                                setTimeout(() => {
                                    modal.classList.remove('opacity-100', 'scale-100');
                                    modal.classList.add('opacity-0', 'scale-90');
                                    backdrop.classList.remove('opacity-100');
                                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                                }, 2000);
                            }
                        };

                        @if (session('success'))
                            showModal('successModalBackdrop', 'successModal');
                        @endif

                        @if (session('error'))
                            showModal('errorModalBackdrop', 'errorModal');
                        @endif
                    });
                    </script>


                        <h2 class="text-3xl font-bold">{{ $firstname }} {{ $lastname }}</h2>
                        <p class="text-lg font-semibold">DEPARTMENT {{ $role }}</p>
                        <p class="text-lg">{{ $yearLevel }} - {{ $block }}</p>
                        <p class="text-lg">{{ strtoupper($gender) }}</p>

                    </div>

                    <div class="mt-8 text-lg flex flex-col items-center text-center">
                        <p class="font-semibold">
                            USERNAME: <span class="font-normal">{{ $username }}</span>
                        </p>

                        <div class="font-semibold flex items-center justify-center mt-2 text-xl">
                            <span>PASSWORD:</span>
                            <input type="password"
                                class="ml-2 font-normal rounded px-1 py-0.5 text-white text-lg w-[20%] bg-green-900 border-none"
                                value="********" readonly>
                                <a href="#" id="openModalBtn">
                                    <i class="fas fa-edit ml-2 text-white cursor-pointer"></i>
                                  </a>

                        </div>
                    </div>


                    <form id="imageUploadForm" action="{{ route('image.upload') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="profileImage" name="image" class="hidden" onchange="previewImage(event)">
                        <input type="hidden" name="student_id" value="{{ $id }}">

                        <button id="uploadBtn" type="button" class="bg-blue-500 text-white px-4 py-2 rounded-md hidden"
                            onclick="confirmUpload()">
                            Upload Image
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div id="popupModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-md shadow-md text-center">
                <p class="text-lg font-semibold mb-4">Are you sure you want to upload this image?</p>
                <button onclick="submitImage()" class="bg-green-500 text-white px-4 py-2 rounded-md">Yes</button>
                <button onclick="closePopup()" class="bg-red-500 text-white px-4 py-2 rounded-md ml-2">Cancel</button>
            </div>
        </div>

        <script>
            function previewImage(event) {
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById("previewImage").src = e.target.result;
                        document.getElementById("uploadBtn").classList.remove("hidden");
                    };
                    reader.readAsDataURL(file);
                }
            }

            function confirmUpload() {
                document.getElementById("popupModal").classList.remove("hidden");
            }

            function closePopup() {
                document.getElementById("popupModal").classList.add("hidden");
            }

            function submitImage() {
                document.getElementById("imageUploadForm").submit();
            }
        </script>



<div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">

    <div class="bg-green-900 text-white rounded-xl w-full max-w-md shadow-xl p-8 relative">

      <button id="closeModalBtn" class="absolute top-4 right-4 text-red-500 hover:text-red-600 text-xl font-bold">&times;</button>

      <h2 class="text-2xl font-bold text-center mb-6">CHANGE PASSWORD</h2>

      <div class="flex w-full h-full items-center justify-center ">
        <div class="w-full max-w-md p-6 rounded ">


            <form method="POST" action="{{ route('password.Repchange') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Old Password:</label>
                        <input type="password" name="current_password" required
                            class="w-full px-4 py-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">New Password:</label>
                        <input type="password" name="new_password" required
                            class="w-full px-4 py-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Confirm New Password:</label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full px-4 py-2 rounded-md text-black border border-gray-300 focus:outline-none focus:ring-2" />
                    </div>
                </div>

                <div class="mt-6 text-center">
                <button type="submit"
                    class="bg-white text-green-900 font-semibold px-6 py-2 rounded-full shadow-md hover:bg-gray-200">
                    Change Password
                </button>
                </div>
            </form>
        </div>
     </div>
    </div>
 </div>

  <script>
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('changePasswordModal');

    openBtn.addEventListener('click', function(event) {
      event.preventDefault();
      modal.style.display = 'flex';
    });

    closeBtn.addEventListener('click', function() {
      modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });
  </script>





    </x-Repre-components.sidebar>
</x-trea-components.content>

