<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tasks = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY created_at DESC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $title, $description]);
    } elseif ($_POST['action'] == 'edit') {
        $task_id = $_POST['task_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $task_id]);
    } elseif ($_POST['action'] == 'delete') {
        $task_id = $_POST['task_id'];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);
    } elseif ($_POST['action'] == 'toggle_status') {
        $task_id = $_POST['task_id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status === 'pending') ? 'completed' : 'pending';
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $task_id]);
    }
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tasks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            background: url('assets/1678169189326.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
        }
        #editModal {
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: bottom 0.3s ease-in-out;
        }
        #editModal.active {
            bottom: 0;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
<header class="flex justify-between items-center p-6 w-full fixed top-0 left-0 px-10 bg-white shadow-md z-50">
    <h1 class="text-gray-800 text-3xl font-extrabold">Task Manager</h1>
    <div class="flex items-center gap-4">
        <!-- Profile Icon -->
        <a href="profile.php?user_id=<?= $_SESSION['user_id'] ?>" 
           class="text-blue-600 text-3xl hover:text-blue-800 transition" 
           title="Profile">
            <i class="ph ph-user-circle"></i>
        </a>

        <!-- Logout Icon -->
        <a href="logout.php" 
           class="text-red-600 text-2xl hover:text-red-800 transition duration-300" 
           title="Logout">
            <i class="ph ph-sign-out"></i>
        </a>
    </div>
</header>



    <main class="w-full max-w-4xl mt-20 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Tasks</h2>
        
        <form method="POST" class="mb-6 flex gap-3">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Task Title" class="flex-1 p-3 border rounded" required>
            <input type="text" name="description" placeholder="Description" class="flex-1 p-3 border rounded" required>
            <button type="submit" class="bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition">
                <i class="ph ph-plus-circle"></i> Add Task
            </button>
        </form>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 border">#</th>
                    <th class="p-3 border">Title</th>
                    <th class="p-3 border">Description</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $index => $task): ?>
                <tr class="border">
                    <td class="p-3 border text-center"><?php echo $index + 1; ?></td>
                    <td class="p-3 border"><?php echo htmlspecialchars($task['title']); ?></td>
                    <td class="p-3 border"><?php echo htmlspecialchars($task['description']); ?></td>
                    <td class="p-3 border text-center">
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $task['status']; ?>">
                            <button type="submit" class="text-lg">
                                <?php if ($task['status'] === 'pending'): ?>
                                    <i class="ph ph-clock text-yellow-500"></i>
                                <?php else: ?>
                                    <i class="ph ph-check-circle text-green-500"></i>
                                <?php endif; ?>
                            </button>
                        </form>
                    </td>
                    <td class="p-3 border text-center flex justify-center gap-3">
                        <button onclick="openEditModal('<?php echo $task['id']; ?>', '<?php echo htmlspecialchars($task['title']); ?>', '<?php echo htmlspecialchars($task['description']); ?>')" class="text-blue-500"><i class="ph ph-pencil"></i></button>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" class="text-red-500"><i class="ph ph-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <div id="editModal">
      <h2 class="text-xl font-bold mb-4">Edit Task</h2>
      <form method="POST" class="flex gap-4">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="editTaskId" name="task_id">
        <input type="text" id="editTitle" name="title" class="flex-1 p-2 border rounded">
        <textarea id="editDescription" name="description" class="flex-1 p-2 border rounded"></textarea>
        <button type="submit" class="bg-blue-600 text-white p-2 rounded">Save</button>
        <button type="button" class="bg-gray-400 text-white p-2 rounded" onclick="closeEditModal()">Cancel</button>
      </form>
    </div>

    <script>
      function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
      }
    </script>

    <script>
        function openEditModal(id, title, description) {
            document.getElementById('editTaskId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editModal').classList.add('active');
        }
    </script>
</body>
</html>
