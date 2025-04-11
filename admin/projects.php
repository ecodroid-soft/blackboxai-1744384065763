<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Require login
require_login();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_flash_message('error', 'Invalid token. Please try again.');
        header('Location: projects.php');
        exit();
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    // Handle image upload
                    $image_result = upload_image($_FILES['image'], 'uploads/projects');
                    if (!$image_result['success']) {
                        throw new Exception($image_result['message']);
                    }

                    $stmt = $db->prepare("INSERT INTO projects (title, description, image_url, category, completion_date) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['title']),
                        sanitize_input($_POST['description']),
                        $image_result['filename'],
                        sanitize_input($_POST['category']),
                        sanitize_input($_POST['completion_date'])
                    ]);
                    
                    set_flash_message('success', 'Project added successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to add project: ' . $e->getMessage());
                }
                break;

            case 'edit':
                try {
                    $image_sql = '';
                    $params = [
                        sanitize_input($_POST['title']),
                        sanitize_input($_POST['description']),
                        sanitize_input($_POST['category']),
                        sanitize_input($_POST['completion_date'])
                    ];

                    // Handle image upload if new image is provided
                    if (!empty($_FILES['image']['name'])) {
                        $image_result = upload_image($_FILES['image'], 'uploads/projects');
                        if (!$image_result['success']) {
                            throw new Exception($image_result['message']);
                        }
                        $image_sql = ', image_url = ?';
                        $params[] = $image_result['filename'];
                    }

                    $params[] = $_POST['id']; // Add ID for WHERE clause

                    $stmt = $db->prepare("UPDATE projects SET title = ?, description = ?, category = ?, completion_date = ?" . $image_sql . " WHERE id = ?");
                    $stmt->execute($params);
                    
                    set_flash_message('success', 'Project updated successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to update project: ' . $e->getMessage());
                }
                break;

            case 'delete':
                try {
                    $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    
                    set_flash_message('success', 'Project deleted successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to delete project: ' . $e->getMessage());
                }
                break;
        }
        
        header('Location: projects.php');
        exit();
    }
}

// Get projects for listing
try {
    $stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
    set_flash_message('error', 'Failed to fetch projects.');
}

// Get project for editing if ID is provided
$edit_project = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $edit_project = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        set_flash_message('error', 'Failed to fetch project for editing.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - <?php echo SITE_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E31E24',
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform transition-transform duration-300 lg:transform-none lg:relative"
             :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            
            <!-- Logo -->
            <div class="p-6 border-b">
                <a href="<?php echo SITE_URL; ?>" class="text-2xl font-bold text-primary">
                    POFINFRAA
                </a>
                <p class="text-sm text-gray-600 mt-1">Admin Portal</p>
            </div>
            
            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="projects.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-primary text-white">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
                <a href="services.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-cogs"></i>
                    <span>Services</span>
                </a>
                <a href="slider.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-images"></i>
                    <span>Slider Images</span>
                </a>
                <a href="settings.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">
                            Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                        </span>
                        <a href="logout.php" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <?php $flash = get_flash_message(); ?>
                <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Manage Projects</h1>
                    <?php if (!isset($_GET['action']) || $_GET['action'] !== 'add'): ?>
                    <a href="?action=add" class="bg-primary hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add New Project
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                <!-- Add/Edit Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">
                        <?php echo $_GET['action'] === 'add' ? 'Add New Project' : 'Edit Project'; ?>
                    </h2>
                    
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                        <?php if ($edit_project): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                        <?php endif; ?>

                        <div>
                            <label for="title" class="block text-gray-700 font-medium mb-2">Project Title</label>
                            <input type="text" id="title" name="title" required
                                   value="<?php echo $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo $edit_project ? htmlspecialchars($edit_project['description']) : ''; ?></textarea>
                        </div>

                        <div>
                            <label for="category" class="block text-gray-700 font-medium mb-2">Category</label>
                            <input type="text" id="category" name="category" required
                                   value="<?php echo $edit_project ? htmlspecialchars($edit_project['category']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="completion_date" class="block text-gray-700 font-medium mb-2">Completion Date</label>
                            <input type="date" id="completion_date" name="completion_date" required
                                   value="<?php echo $edit_project ? $edit_project['completion_date'] : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-medium mb-2">
                                Project Image <?php echo $edit_project ? '(Leave empty to keep current image)' : ''; ?>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" <?php echo !$edit_project ? 'required' : ''; ?>
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="projects.php" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                <?php echo $_GET['action'] === 'add' ? 'Add Project' : 'Update Project'; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Projects List -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($projects)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No projects found. <a href="?action=add" class="text-primary hover:text-red-700">Add one now</a>.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded object-cover" 
                                                 src="../uploads/projects/<?php echo htmlspecialchars($project['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($project['title']); ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($project['title']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo truncate_text($project['description'], 50); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($project['category']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo format_date($project['completion_date']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="?action=edit&id=<?php echo $project['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" class="inline-block" 
                                              onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
