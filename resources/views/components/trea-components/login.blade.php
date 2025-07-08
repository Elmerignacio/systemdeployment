
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center relative">

    <div class="absolute inset-0 bg-cover bg-center opacity-80 bg-no-repeat bg-fixed"
    style="background-image: url('{{ asset('images/login.png') }}');">
</div>

    <div class="text-center bg-opacity-90 rounded-lg p-4 sm:p-6 md:p-8 w-full sm:w-[60%] md:w-[40%] lg:w-[30%] relative">

        <img src="{{ asset('images/gccLogo.png') }}" 
             alt="Logo" class="mx-auto mb-4 logo object-contain w-[40%] h-[40%]">
        

</body>
</html>
            