<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('assets/1678169189326.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for better readability */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
</head>
<body> 
    <div class="overlay"></div>

    <!-- Header -->
    <header class="flex justify-between items-center p-6 w-full fixed top-0 left-0 px-10">
        <h1 class="text-white text-5xl font-extrabold">Tasks</h1>
        <a href="signup.php" class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-blue-700">Start Now</a>
    </header>
    
   <!-- Main Content -->
   <div class="flex flex-col items-center justify-center flex-grow text-center">
        <p class="text-lg text-white font-extrabold">Welcome to Tasks</p>
        <h2 class="text-5xl font-extrabold mt-2 text-white">Manage your tasks with ease</h2>
        
        <div class="mt-6 space-x-4">
            <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg">Sign Up</a>
            <a href="login.php" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg">Log In</a>
        </div>
    </div>
  
</body>
</html>
