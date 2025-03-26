<?php
# Create an account for a new user

require('includes/db.php');

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if email is already registered
  $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $checkStmt->execute([$email]);
  if ($checkStmt->fetch()) {
    // Email already exists
    $error_message = "Email already registered! Please log in.";
  } else {
    // Proceed with inserting the new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$fullname, $email, $password])) {
      header('Location: login.php');
      exit;
    } else {
      error_log($stmt->errorInfo()[2]);
      $error_message = "Error creating account. Please try again.";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Tasks</title>
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
    .glassmorphic-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 2.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 400px;
    }
    .form-input {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 8px;
      border: none;
      background: rgba(255, 255, 255, 0.3);
      outline: none;
      color: #000;
    }
    .error-message {
      color: red;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }
  </style>
</head>
<body> 
  <!-- Header -->
  <header class="flex justify-between items-center p-6 w-full fixed top-0 left-0 px-10">
    <h1 class="text-white text-5xl font-extrabold">Tasks</h1>
    <a href="index.php" class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">Home</a>
  </header>
  
  <!-- Sign Up Form -->
  <div class="flex flex-col items-center justify-center flex-grow text-center">
    <div class="glassmorphic-container">
      <h2 class="text-3xl font-bold text-white mb-6">Sign Up</h2>
      <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>
      <form action="signup.php" method="POST">
        <input type="text" name="fullname" placeholder="Name" class="form-input" required>
        <input type="email" name="email" placeholder="Email" class="form-input" required>
        <input type="password" name="password" placeholder="Password" class="form-input" required>
        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition duration-300">Sign Up</button>
      </form>
      <p class="mt-4 text-gray-800">Already registered? <a href="login.php" class="text-blue-800 font-bold hover:underline">Login</a></p>
    </div>
  </div>
</body>
</html>
