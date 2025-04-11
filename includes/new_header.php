<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Infrastructure Excellence</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D4AF37',  // Gold color from the logo
                        secondary: '#222222' // Dark gray for contrast
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
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-md" x-data="{ isOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo SITE_URL; ?>" class="flex flex-col items-center">
                            <img src="assets/images/logo.png" alt="POFINFRAA Logo" class="h-16">
                            <span class="text-xl font-bold text-primary mt-1">POFINFRAA</span>
                            <span class="text-xs text-secondary">REAL ESTATE AND INFRASTRUCTURE</span>
                        </a>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="<?php echo SITE_URL; ?>" class="nav-link text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">Home</a>
                    <a href="about.php" class="nav-link text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">About</a>
                    <a href="index.php#services" class="nav-link text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">Services</a>
                    <a href="index.php#projects" class="nav-link text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">Projects</a>
                    <a href="contact.php" class="nav-link text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">Contact</a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="isOpen = !isOpen" class="text-gray-700 hover:text-primary">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-show="isOpen" class="md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?php echo SITE_URL; ?>" class="block text-gray-700 hover:text-primary px-3 py-2 text-base font-medium">Home</a>
                <a href="about.php" class="block text-gray-700 hover:text-primary px-3 py-2 text-base font-medium">About</a>
                <a href="index.php#services" class="block text-gray-700 hover:text-primary px-3 py-2 text-base font-medium">Services</a>
                <a href="index.php#projects" class="block text-gray-700 hover:text-primary px-3 py-2 text-base font-medium">Projects</a>
                <a href="contact.php" class="block text-gray-700 hover:text-primary px-3 py-2 text-base font-medium">Contact</a>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php $flash = get_flash_message(); ?>
    <?php if ($flash): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="rounded-md p-4 <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
            <?php echo $flash['message']; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow">
