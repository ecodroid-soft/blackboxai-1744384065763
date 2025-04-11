<?php 
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/new_header.php';
?>

<!-- Hero Section with Slider -->
<div class="relative h-[600px] overflow-hidden">
    <!-- Slider container -->
    <div class="relative h-full" x-data="{ currentSlide: 0 }" x-init="setInterval(() => currentSlide = currentSlide === 2 ? 0 : currentSlide + 1, 5000)">
        <!-- Slide 1 -->
        <div class="absolute inset-0 transition-opacity duration-1000" :class="{ 'opacity-0': currentSlide !== 0 }">
            <img src="assets/images/slider1.jpg" alt="Infrastructure Project 1" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Building Tomorrow's Infrastructure</h1>
                    <p class="text-xl md:text-2xl mb-8">Leading the way in sustainable development</p>
                    <a href="#contact" class="bg-primary hover:bg-red-700 text-white px-8 py-3 rounded-md text-lg font-medium transition-colors">
                        Get Started
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="absolute inset-0 transition-opacity duration-1000" :class="{ 'opacity-0': currentSlide !== 1 }">
            <img src="assets/images/slider2.jpg" alt="Infrastructure Project 2" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Excellence in Construction</h1>
                    <p class="text-xl md:text-2xl mb-8">Quality and innovation in every project</p>
                    <a href="#projects" class="bg-primary hover:bg-red-700 text-white px-8 py-3 rounded-md text-lg font-medium transition-colors">
                        View Projects
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="absolute inset-0 transition-opacity duration-1000" :class="{ 'opacity-0': currentSlide !== 2 }">
            <img src="assets/images/slider3.jpg" alt="Infrastructure Project 3" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Sustainable Solutions</h1>
                    <p class="text-xl md:text-2xl mb-8">Building with environmental responsibility</p>
                    <a href="#about" class="bg-primary hover:bg-red-700 text-white px-8 py-3 rounded-md text-lg font-medium transition-colors">
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Slider Controls -->
        <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <button @click="currentSlide = 0" class="w-3 h-3 rounded-full transition-colors" 
                    :class="currentSlide === 0 ? 'bg-primary' : 'bg-white'"></button>
            <button @click="currentSlide = 1" class="w-3 h-3 rounded-full transition-colors" 
                    :class="currentSlide === 1 ? 'bg-primary' : 'bg-white'"></button>
            <button @click="currentSlide = 2" class="w-3 h-3 rounded-full transition-colors" 
                    :class="currentSlide === 2 ? 'bg-primary' : 'bg-white'"></button>
        </div>
    </div>
</div>

<!-- About Section -->
<section id="about" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">About POFINFRAA</h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <img src="assets/images/about.jpg" alt="About POFINFRAA" class="rounded-lg shadow-xl">
            </div>
            <div>
                <h3 class="text-2xl font-bold mb-4">Leading Infrastructure Development</h3>
                <p class="text-gray-600 mb-6">
                    With years of experience in the infrastructure sector, POFINFRAA has established itself as a leader
                    in delivering high-quality construction and development projects. Our commitment to excellence,
                    innovation, and sustainability sets us apart in the industry.
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-primary mb-2">150+</div>
                        <div class="text-gray-600">Projects Completed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-primary mb-2">15+</div>
                        <div class="text-gray-600">Years Experience</div>
                    </div>
                </div>
                <a href="#contact" class="inline-block bg-primary hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Our Services</h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Service 1 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-primary text-4xl mb-4">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Construction Management</h3>
                <p class="text-gray-600">
                    Professional management of construction projects from inception to completion,
                    ensuring quality, timeliness, and cost-effectiveness.
                </p>
            </div>

            <!-- Service 2 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-primary text-4xl mb-4">
                    <i class="fas fa-road"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Infrastructure Development</h3>
                <p class="text-gray-600">
                    Comprehensive infrastructure development services including roads,
                    bridges, and urban development projects.
                </p>
            </div>

            <!-- Service 3 -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-primary text-4xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Project Planning</h3>
                <p class="text-gray-600">
                    Strategic project planning and consulting services to ensure
                    successful project execution and delivery.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section id="projects" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Our Projects</h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Project 1 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <img src="assets/images/project1.jpg" alt="Project 1" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Highway Construction</h3>
                    <p class="text-gray-600 mb-4">Major highway development project connecting key urban centers.</p>
                    <a href="#" class="text-primary hover:text-red-700 font-medium">Learn More →</a>
                </div>
            </div>

            <!-- Project 2 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <img src="assets/images/project2.jpg" alt="Project 2" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Bridge Construction</h3>
                    <p class="text-gray-600 mb-4">Modern bridge construction project improving urban connectivity.</p>
                    <a href="#" class="text-primary hover:text-red-700 font-medium">Learn More →</a>
                </div>
            </div>

            <!-- Project 3 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <img src="assets/images/project3.jpg" alt="Project 3" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Urban Development</h3>
                    <p class="text-gray-600 mb-4">Comprehensive urban infrastructure development project.</p>
                    <a href="#" class="text-primary hover:text-red-700 font-medium">Learn More →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Contact Us</h2>
            <div class="w-20 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form action="process_contact.php" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Get in Touch</h3>
                    <p class="text-gray-600">
                        Have questions about our services? Contact us today and our team will be happy to assist you.
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="text-primary text-xl mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Address</h4>
                            <p class="text-gray-600">123 Infrastructure Road, City, Country</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-primary text-xl mt-1">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Phone</h4>
                            <p class="text-gray-600">+1 234 567 8900</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="text-primary text-xl mt-1">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Email</h4>
                            <p class="text-gray-600">info@pofinfraa.com</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-600 hover:text-primary text-2xl transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary text-2xl transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary text-2xl transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary text-2xl transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
