<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Require login
require_login();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_flash_message('error', 'Invalid token. Please try again.');
        header('Location: slider.php');
        exit();
    }

    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    // Upload image
                    $result = upload_image($_FILES['image'], 'sliders');
                    if (!$result['success']) {
                        throw new Exception($result['message']);
                    }
                    
                    // Insert into database
                    $stmt = $db->prepare("INSERT INTO slider_images (title, image_url) VALUES (?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['title']),
                        $result['filename']
                    ]);
                    set_flash_message('success', 'Slider image added successfully.');
                    break;

                case 'delete':
                    // Get image filename before deleting record
                    $stmt = $db->prepare("SELECT image_url FROM slider_images WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $image = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($image) {
                        // Delete from database
                        $stmt = $db->prepare("DELETE FROM slider_images WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        // Delete image file
                        delete_image($image['image_url'], SLIDER_UPLOAD_PATH);
                        set_flash_message('success', 'Slider image deleted successfully.');
                    }
                    break;

                case 'toggle':
                    $stmt = $db->prepare("UPDATE slider_images SET active = NOT active WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    set_flash_message('success', 'Slider status updated successfully.');
                    break;
            }
        }
    } catch (Exception $e) {
        set_flash_message('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: slider.php');
    exit();
}

// Get all slider images
try {
    $stmt = $db->query("SELECT * FROM slider_images ORDER BY created_at DESC");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sliders = [];
    set_flash_message('error', 'Failed to fetch slider images.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sliders - <?php echo SITE_NAME; ?></title>
    
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

                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Manage Slider Images</h1>
                    <p class="text-gray-600 mt-1">Add, remove, or update slider images for your website.</p>
                </div>

                <!-- Add New Slider Form -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Add New Slider</h2>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="add">

                        <div>
                            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-medium mb-2">Image</label>
                            <input type="file" id="image" name="image" required accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <p class="mt-1 text-sm text-gray-500">Maximum file size: 5MB. Allowed formats: JPG, JPEG, PNG, GIF</p>
                        </div>

                        <div>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                Add Slider
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Slider Images List -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Current Sliders</h2>
                    
                    <?php if (empty($sliders)): ?>
                    <p class="text-gray-600">No slider images found.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($sliders as $slider): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <img src="<?php echo SLIDER_UPLOAD_URL . $slider['image_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($slider['title']); ?>"
                                 class="w-full h-48 object-cover">
                            
                            <div class="p-4">
                                <h3 class="font-medium"><?php echo htmlspecialchars($slider['title']); ?></h3>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <form action="" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                        <button type="submit" class="text-sm <?php echo $slider['active'] ? 'text-green-600' : 'text-gray-600'; ?>">
                                            <?php echo $slider['active'] ? 'Active' : 'Inactive'; ?>
                                        </button>
                                    </form>
                                    
                                    <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
