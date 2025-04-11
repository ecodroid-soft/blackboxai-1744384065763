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
        header('Location: settings.php');
        exit();
    }

    try {
        // Check if company info exists
        $stmt = $db->query("SELECT COUNT(*) FROM company_info");
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Update existing record
            $stmt = $db->prepare("UPDATE company_info SET 
                company_name = ?, 
                email = ?, 
                phone = ?, 
                address = ?, 
                about_text = ?
                WHERE id = 1");
        } else {
            // Insert new record
            $stmt = $db->prepare("INSERT INTO company_info 
                (company_name, email, phone, address, about_text) 
                VALUES (?, ?, ?, ?, ?)");
        }

        $stmt->execute([
            sanitize_input($_POST['company_name']),
            sanitize_input($_POST['email']),
            sanitize_input($_POST['phone']),
            sanitize_input($_POST['address']),
            sanitize_input($_POST['about_text'])
        ]);
        
        set_flash_message('success', 'Settings updated successfully.');
    } catch (Exception $e) {
        set_flash_message('error', 'Failed to update settings: ' . $e->getMessage());
    }
    
    header('Location: settings.php');
    exit();
}

// Get current settings
try {
    $stmt = $db->query("SELECT * FROM company_info LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $settings = [];
    set_flash_message('error', 'Failed to fetch settings.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
    
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
                <a href="slider.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-images"></i>
                    <span>Slider Images</span>
                </a>
                <a href="settings.php" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-primary text-white">
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
                    <h1 class="text-2xl font-bold">Company Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your company information and contact details.</p>
                </div>

                <!-- Settings Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_name" class="block text-gray-700 font-medium mb-2">Company Name</label>
                                <input type="text" id="company_name" name="company_name" required
                                       value="<?php echo $settings ? htmlspecialchars($settings['company_name']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo $settings ? htmlspecialchars($settings['email']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required
                                       value="<?php echo $settings ? htmlspecialchars($settings['phone']) : '+91 84597-00000'; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                                <input type="text" id="address" name="address" required
                                       value="<?php echo $settings ? htmlspecialchars($settings['address']) : 'B-290, Street Number-1, Chattarpur Enclave Phase 2, New Delhi - 110074'; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>

                        <div>
                            <label for="about_text" class="block text-gray-700 font-medium mb-2">About Text</label>
                            <textarea id="about_text" name="about_text" rows="6" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo $settings ? htmlspecialchars($settings['about_text']) : ''; ?></textarea>
                            <p class="mt-1 text-sm text-gray-500">This text will appear in the About section of your website.</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Additional Settings -->
                <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Admin Account</h2>
                    <p class="text-gray-600 mb-4">Change your admin account password to maintain security.</p>
                    
                    <a href="change-password.php" class="inline-flex items-center space-x-2 text-primary hover:text-red-700">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </a>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
