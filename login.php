<?php
# logs in old users
session_start();
require('includes/db.php');

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    header('Location: dashboard.php');
    exit();
  } else {
    $error_message = "Invalid email or password";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tasks</title>
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
        }
        .glassmorphic {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
        }
    </style>
</head>
<body> 
    <!-- Header -->
    <header class="flex justify-between items-center p-6 w-full fixed top-0 left-0 px-10">
        <h1 class="text-gray-800 text-5xl font-extrabold">Tasks</h1>
        <a href="index.php" class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">Home</a>
    </header>
    
    <!-- Login Form -->
    <div class="flex flex-col items-center justify-center flex-grow text-center">
        <div class="glassmorphic">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Login</h2>
            <?php if (!empty($error_message)): ?>
                <div class="text-red-600 mb-4"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" class="w-full p-3 border rounded mb-3" required>
                <input type="password" name="password" placeholder="Password" class="w-full p-3 border rounded mb-3" required>
                <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition duration-300">Login</button>
            </form>
            <p class="mt-4 text-gray-800">Don't have an account? <a href="signup.php" class="text-blue-800 font-bold hover:underline">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
