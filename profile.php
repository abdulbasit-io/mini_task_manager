<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user_id = $_SESSION['user_id'];

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = (int)$_GET['user_id'];

if ($user_id !== $logged_in_user_id) {
    die("Unauthorized access.");
}

$success_message = '';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$fullname, $email, $hashedPassword, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
            $stmt->execute([$fullname, $email, $user_id]);
        }
        $success_message = "Profile updated successfully!";
    } catch (PDOException $e) {
        // You could log $e->getMessage() to a log file for debugging
        $success_message = "Something went wrong. Please try again.";
    }
}

// Always fetch user data (either initially or after update)
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <style>
    body {
      background: url('assets/1678169189326.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .glassmorphic-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 2.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
      color: white;
    }
    input {
      background: rgba(255, 255, 255, 0.3);
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      width: 100%;
      color: black;
    }
    input::placeholder {
      color: rgba(0, 0, 0, 0.5);
    }
    label {
      color: white;
    }
  </style>
</head>
<body>
  <div class="glassmorphic-container">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-3xl font-bold">Your Profile</h2>
      <a href="dashboard.php" title="Back to Dashboard" class="text-white hover:text-gray-300 text-xl">
        <i class="ph ph-arrow-left"></i>
      </a>
    </div>

    <?php if (!empty($success_message)): ?>
      <div class="mb-4 bg-green-200 text-green-900 p-3 rounded shadow text-sm">
        <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>

    <!-- Profile Display Section -->
    <div id="viewProfile" class="space-y-4">
      <div>
        <p class="text-sm text-gray-200">Name</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($user['fullname']) ?></p>
      </div>
      <div>
        <p class="text-sm text-gray-200">Email</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($user['email']) ?></p>
      </div>
      <button onclick="toggleEdit()" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition duration-300">
        <i class="ph ph-pencil mr-1"></i> Edit Profile
      </button>
    </div>

    <!-- Edit Profile Form -->
    <form method="POST" action="profile.php?user_id=<?= $user_id ?>" 
          id="editForm" 
          class="space-y-4 mt-4 transform transition duration-300 ease-out opacity-0 scale-95 hidden">
      <div>
        <label for="name" class="block text-sm font-semibold mb-1">Name</label>
        <input type="text" id="name" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
      </div>

      <div>
        <label for="email" class="block text-sm font-semibold mb-1">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold mb-1">
          Password <span class="text-sm text-gray-200">(Leave blank to keep current password)</span>
        </label>
        <input type="password" id="password" name="password" placeholder="New password (optional)">
      </div>

      <div class="flex justify-between items-center mt-4">
        <button type="button" onclick="toggleEdit()" class="text-sm text-white hover:underline">
          Cancel
        </button>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow transition duration-300">
          Save Changes
        </button>
      </div>
    </form>
  </div>

  <script>
    function toggleEdit() {
      const view = document.getElementById('viewProfile');
      const form = document.getElementById('editForm');

      if (form.classList.contains('hidden')) {
        view.classList.add('hidden');
        form.classList.remove('hidden');
        setTimeout(() => {
          form.classList.remove('opacity-0', 'scale-95');
          form.classList.add('opacity-100', 'scale-100');
        }, 10);
      } else {
        form.classList.remove('opacity-100', 'scale-100');
        form.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
          form.classList.add('hidden');
          view.classList.remove('hidden');
        }, 300);
      }
    }
  </script>
</body>
</html>
