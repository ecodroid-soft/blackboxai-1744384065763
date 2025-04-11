<?php 
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/new_header.php';
?>

<!-- About Us Hero Section -->
<div class="relative h-[400px] overflow-hidden">
    <div class="absolute inset-0">
        <img src="assets/images/about-hero.jpg" alt="About Us Hero" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">About POFINFRAA</h1>
                <p class="text-xl md:text-2xl">Leading the way in infrastructure development</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Company Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="text-3xl font-bold mb-6">Our Story</h2>
                <div class="prose prose-lg">
                    <p class="text-gray-600 mb-6">
                        POFINFRAA has been at the forefront of infrastructure development for over 15 years, 
                        delivering excellence in construction and project management. Our commitment to quality, 
                        innovation, and sustainability has made us a trusted partner in building tomorrow's infrastructure.
                    </p>
                    <p class="text-gray-600">
                        We specialize in delivering comprehensive infrastructure solutions that meet the evolving needs 
                        of our clients and communities. From concept to completion, we ensure every project reflects our 
                        dedication to excellence and sustainable development.
                    </p>
                </div>
            </div>
            <div>
                <img src="assets/images/about-company.jpg" alt="Company Overview" class="rounded-lg shadow-xl">
            </div>
        </div>

        <!-- Vision & Mission -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-primary text-4xl mb-4">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Our Vision</h3>
                <p class="text-gray-600">
                    To be the leading infrastructure development company, recognized for excellence, 
                    innovation, and sustainable solutions that shape the future of communities worldwide.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-primary text-4xl mb-4">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Our Mission</h3>
                <p class="text-gray-600">
                    To deliver high-quality infrastructure projects that exceed expectations, foster sustainable 
                    development, and create lasting value for our clients and communities through innovation 
                    and excellence.
                </p>
            </div>
        </div>

        <!-- Core Values -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold text-center mb-12">Our Core Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-primary text-4xl mb-4">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Excellence</h3>
                    <p class="text-gray-600">Striving for the highest standards in everything we do</p>
                </div>
                <div class="text-center">
                    <div class="text-primary text-4xl mb-4">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Innovation</h3>
                    <p class="text-gray-600">Embracing new ideas and solutions for better outcomes</p>
                </div>
                <div class="text-center">
                    <div class="text-primary text-4xl mb-4">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Integrity</h3>
                    <p class="text-gray-600">Maintaining the highest ethical standards in all our dealings</p>
                </div>
                <div class="text-center">
                    <div class="text-primary text-4xl mb-4">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Sustainability</h3>
                    <p class="text-gray-600">Committed to environmental and social responsibility</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div>
            <h2 class="text-3xl font-bold text-center mb-12">Our Leadership Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="assets/images/team1.jpg" alt="Team Member 1" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">John Smith</h3>
                        <p class="text-gray-600 mb-4">Chief Executive Officer</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="assets/images/team2.jpg" alt="Team Member 2" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Sarah Johnson</h3>
                        <p class="text-gray-600 mb-4">Chief Operations Officer</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="assets/images/team3.jpg" alt="Team Member 3" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Michael Brown</h3>
                        <p class="text-gray-600 mb-4">Chief Technical Officer</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
