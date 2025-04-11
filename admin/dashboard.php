<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Require login
require_login();

// Get statistics
try {
    // Count projects
    $stmt = $db->query("SELECT COUNT(*) FROM projects");
    $total_projects = $stmt->fetchColumn();
    
    // Count services
    $stmt = $db->query("SELECT COUNT(*) FROM services");
    $total_services = $stmt->fetchColumn();
    
    // Count slider images
    $stmt = $db->query("SELECT COUNT(*) FROM slider_images");
    $total_sliders = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    $total_projects = 0;
    $total_services = 0;
    $total_sliders = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
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
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-primary text-white">
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

                <h1 class="text-2xl font-bold mb-6">Dashboard Overview</h1>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Projects Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Total Projects</p>
                                <h3 class="text-3xl font-bold"><?php echo $total_projects; ?></h3>
                            </div>
                            <div class="text-primary text-3xl">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                        </div>
                        <a href="projects.php" class="mt-4 inline-block text-primary hover:text-red-700">
                            Manage Projects →
                        </a>
                    </div>

                    <!-- Services Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Total Services</p>
                                <h3 class="text-3xl font-bold"><?php echo $total_services; ?></h3>
                            </div>
                            <div class="text-primary text-3xl">
                                <i class="fas fa-cogs"></i>
                            </div>
                        </div>
                        <a href="services.php" class="mt-4 inline-block text-primary hover:text-red-700">
                            Manage Services →
                        </a>
                    </div>

                    <!-- Slider Images Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600">Slider Images</p>
                                <h3 class="text-3xl font-bold"><?php echo $total_sliders; ?></h3>
                            </div>
                            <div class="text-primary text-3xl">
                                <i class="fas fa-images"></i>
                            </div>
                        </div>
                        <a href="slider.php" class="mt-4 inline-block text-primary hover:text-red-700">
                            Manage Sliders →
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="projects.php?action=add" class="flex items-center space-x-2 p-4 rounded-lg border border-gray-200 hover:border-primary hover:text-primary transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add New Project</span>
                        </a>
                        <a href="services.php?action=add" class="flex items-center space-x-2 p-4 rounded-lg border border-gray-200 hover:border-primary hover:text-primary transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add New Service</span>
                        </a>
                        <a href="slider.php?action=add" class="flex items-center space-x-2 p-4 rounded-lg border border-gray-200 hover:border-primary hover:text-primary transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add Slider Image</span>
                        </a>
                        <a href="settings.php" class="flex items-center space-x-2 p-4 rounded-lg border border-gray-200 hover:border-primary hover:text-primary transition-colors">
                            <i class="fas fa-cog"></i>
                            <span>Update Settings</span>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
