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
        header('Location: projects.php');
        exit();
    }

    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    // Upload image
                    $image_url = upload_image($_FILES['image'], PROJECT_UPLOAD_PATH);
                    
                    // Insert into database
                    $stmt = $db->prepare("INSERT INTO projects (title, description, image_url, category, completion_date) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['title']),
                        sanitize_input($_POST['description']),
                        $image_url,
                        sanitize_input($_POST['category']),
                        sanitize_input($_POST['completion_date'])
                    ]);
                    set_flash_message('success', 'Project added successfully.');
                    break;

                case 'delete':
                    // Get image filename before deleting record
                    $stmt = $db->prepare("SELECT image_url FROM projects WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $project = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($project) {
                        // Delete from database
                        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        
                        // Delete image file
                        delete_image($project['image_url'], PROJECT_UPLOAD_PATH);
                        set_flash_message('success', 'Project deleted successfully.');
                    }
                    break;

                case 'edit':
                    $update_fields = [
                        'title' => sanitize_input($_POST['title']),
                        'description' => sanitize_input($_POST['description']),
                        'category' => sanitize_input($_POST['category']),
                        'completion_date' => sanitize_input($_POST['completion_date'])
                    ];

                    // Handle image update if new image uploaded
                    if (!empty($_FILES['image']['name'])) {
                        // Get old image to delete
                        $stmt = $db->prepare("SELECT image_url FROM projects WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $project = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Upload new image
                        $image_url = upload_image($_FILES['image'], PROJECT_UPLOAD_PATH);
                        $update_fields['image_url'] = $image_url;
                        
                        // Delete old image
                        if ($project) {
                            delete_image($project['image_url'], PROJECT_UPLOAD_PATH);
                        }
                    }

                    // Build update query
                    $sql = "UPDATE projects SET " . implode(" = ?, ", array_keys($update_fields)) . " = ? WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([...array_values($update_fields), $_POST['id']]);
                    
                    set_flash_message('success', 'Project updated successfully.');
                    break;
            }
        }
    } catch (Exception $e) {
        set_flash_message('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: projects.php');
    exit();
}

// Get all projects
try {
    $stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
    set_flash_message('error', 'Failed to fetch projects.');
}

// Project categories
$categories = ['Commercial', 'Residential', 'Infrastructure', 'Industrial'];
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
    <div x-data="{ sidebarOpen: false, editModal: false, selectedProject: null }">
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

                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Manage Projects</h1>
                    <p class="text-gray-600 mt-1">Add, edit, or remove projects from your portfolio.</p>
                </div>

                <!-- Add New Project Form -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Add New Project</h2>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="add">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-gray-700 font-medium mb-2">Project Title</label>
                                <input type="text" id="title" name="title" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label for="category" class="block text-gray-700 font-medium mb-2">Category</label>
                                <select id="category" name="category" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="completion_date" class="block text-gray-700 font-medium mb-2">Completion Date</label>
                                <input type="date" id="completion_date" name="completion_date" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label for="image" class="block text-gray-700 font-medium mb-2">Project Image</label>
                                <input type="file" id="image" name="image" required accept="image/*"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <p class="mt-1 text-sm text-gray-500">Maximum file size: 5MB. Allowed formats: JPG, JPEG, PNG, GIF</p>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        </div>

                        <div>
                            <button type="submit" class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                Add Project
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Projects List -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Current Projects</h2>
                    
                    <?php if (empty($projects)): ?>
                    <p class="text-gray-600">No projects found.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($projects as $project): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <img src="<?php echo SITE_URL . '/' . PROJECT_UPLOAD_PATH . $project['image_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($project['title']); ?>"
                                 class="w-full h-48 object-cover">
                            
                            <div class="p-4">
                                <h3 class="font-medium"><?php echo htmlspecialchars($project['title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($project['category']); ?></p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Completed: <?php echo date('M Y', strtotime($project['completion_date'])); ?>
                                </p>
                                
                                <div class="mt-4 flex items-center justify-end space-x-4">
                                    <button @click="editModal = true; selectedProject = <?php echo htmlspecialchars(json_encode($project)); ?>"
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
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

                <!-- Edit Project Modal -->
                <div x-show="editModal" 
                     class="fixed inset-0 z-50 overflow-y-auto"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black opacity-50"></div>
                        
                        <div class="relative bg-white rounded-lg max-w-lg w-full p-6">
                            <h3 class="text-xl font-bold mb-4">Edit Project</h3>
                            
                            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" x-bind:value="selectedProject?.id">

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Project Title</label>
                                    <input type="text" name="title" required x-bind:value="selectedProject?.title"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Category</label>
                                    <select name="category" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>" 
                                                x-bind:selected="selectedProject?.category === '<?php echo $category; ?>'">
                                            <?php echo $category; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Completion Date</label>
                                    <input type="date" name="completion_date" required x-bind:value="selectedProject?.completion_date"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Project Image</label>
                                    <input type="file" name="image" accept="image/*"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Description</label>
                                    <textarea name="description" rows="4" required x-text="selectedProject?.description"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                </div>

                                <div class="flex justify-end space-x-4">
                                    <button type="button" @click="editModal = false"
                                            class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-6 py-2 bg-primary hover:bg-red-700 text-white rounded-md transition-colors">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
