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
        header('Location: slider.php');
        exit();
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    // Handle image upload
                    $image_result = upload_image($_FILES['image'], 'uploads/slider');
                    if (!$image_result['success']) {
                        throw new Exception($image_result['message']);
                    }

                    $stmt = $db->prepare("INSERT INTO slider_images (title, image_url, active) VALUES (?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['title']),
                        $image_result['filename'],
                        isset($_POST['active']) ? 1 : 0
                    ]);
                    
                    set_flash_message('success', 'Slider image added successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to add slider image: ' . $e->getMessage());
                }
                break;

            case 'edit':
                try {
                    $image_sql = '';
                    $params = [
                        sanitize_input($_POST['title']),
                        isset($_POST['active']) ? 1 : 0
                    ];

                    // Handle image upload if new image is provided
                    if (!empty($_FILES['image']['name'])) {
                        $image_result = upload_image($_FILES['image'], 'uploads/slider');
                        if (!$image_result['success']) {
                            throw new Exception($image_result['message']);
                        }
                        $image_sql = ', image_url = ?';
                        $params[] = $image_result['filename'];
                    }

                    $params[] = $_POST['id']; // Add ID for WHERE clause

                    $stmt = $db->prepare("UPDATE slider_images SET title = ?, active = ?" . $image_sql . " WHERE id = ?");
                    $stmt->execute($params);
                    
                    set_flash_message('success', 'Slider image updated successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to update slider image: ' . $e->getMessage());
                }
                break;

            case 'delete':
                try {
                    $stmt = $db->prepare("DELETE FROM slider_images WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    
                    set_flash_message('success', 'Slider image deleted successfully.');
                } catch (Exception $e) {
                    set_flash_message('error', 'Failed to delete slider image: ' . $e->getMessage());
                }
                break;
        }
        
        header('Location: slider.php');
        exit();
    }
}

// Get slider images for listing
try {
    $stmt = $db->query("SELECT * FROM slider_images ORDER BY created_at DESC");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sliders = [];
    set_flash_message('error', 'Failed to fetch slider images.');
}

// Get slider for editing if ID is provided
$edit_slider = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM slider_images WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $edit_slider = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        set_flash_message('error', 'Failed to fetch slider image for editing.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slider - <?php echo SITE_NAME; ?></title>
    
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
                <a href="projects.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
                <a href="services.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-cogs"></i>
                    <span>Services</span>
                </a>
                <a href="slider.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-primary text-white">
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
                    <h1 class="text-2xl font-bold">Manage Slider Images</h1>
                    <?php if (!isset($_GET['action']) || $_GET['action'] !== 'add'): ?>
                    <a href="?action=add" class="bg-primary hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add New Slider
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
                <!-- Add/Edit Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">
                        <?php echo $_GET['action'] === 'add' ? 'Add New Slider Image' : 'Edit Slider Image'; ?>
                    </h2>
                    
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                        <?php if ($edit_slider): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_slider['id']; ?>">
                        <?php endif; ?>

                        <div>
                            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                            <input type="text" id="title" name="title" required
                                   value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['title']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-medium mb-2">
                                Image <?php echo $edit_slider ? '(Leave empty to keep current image)' : ''; ?>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" <?php echo !$edit_slider ? 'required' : ''; ?>
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <p class="mt-1 text-sm text-gray-500">Recommended size: 1920x600 pixels</p>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="active" name="active" 
                                   <?php echo (!$edit_slider || $edit_slider['active']) ? 'checked' : ''; ?>
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <label for="active" class="ml-2 text-gray-700">Active (Show in slider)</label>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="slider.php" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                <?php echo $_GET['action'] === 'add' ? 'Add Slider' : 'Update Slider'; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Sliders List -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        <?php if (empty($sliders)): ?>
                        <div class="col-span-full text-center text-gray-500">
                            No slider images found. <a href="?action=add" class="text-primary hover:text-red-700">Add one now</a>.
                        </div>
                        <?php else: ?>
                        <?php foreach ($sliders as $slider): ?>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <img src="../uploads/slider/<?php echo htmlspecialchars($slider['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($slider['title']); ?>"
                                 class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($slider['title']); ?>
                                </h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm <?php echo $slider['active'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                        <?php echo $slider['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <div class="flex space-x-3">
                                        <a href="?action=edit&id=<?php echo $slider['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" class="inline-block" 
                                              onsubmit="return confirm('Are you sure you want to delete this slider image?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
