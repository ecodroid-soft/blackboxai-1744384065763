<?php 
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/new_header.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        set_flash_message('error', 'Invalid token. Please try again.');
        header('Location: contact.php');
        exit();
    }

    try {
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize_input($_POST['name']),
            sanitize_input($_POST['email']),
            sanitize_input($_POST['phone']),
            sanitize_input($_POST['subject']),
            sanitize_input($_POST['message'])
        ]);
        
        set_flash_message('success', 'Thank you for your message. We will get back to you soon.');
    } catch (Exception $e) {
        set_flash_message('error', 'Failed to send message. Please try again.');
    }
    
    header('Location: contact.php');
    exit();
}

// Get company info for contact details
try {
    $stmt = $db->query("SELECT * FROM company_info LIMIT 1");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $company = [];
}
?>

<!-- Contact Hero Section -->
<div class="relative h-[400px] overflow-hidden">
    <div class="absolute inset-0">
        <img src="assets/images/contact-hero.jpg" alt="Contact Us Hero" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Contact Us</h1>
                <p class="text-xl md:text-2xl">Get in touch with our team</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        <?php $flash = get_flash_message(); ?>
        <?php if ($flash): ?>
        <div class="mb-8 p-4 rounded-lg <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
            <?php echo $flash['message']; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Contact Information -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Contact Information</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="text-primary text-xl mt-1">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold mb-1">Address</h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($company['address'] ?? 'B-290, Street Number-1, Chattarpur Enclave Phase 2, New Delhi - 110074'); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="text-primary text-xl mt-1">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold mb-1">Phone</h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($company['phone'] ?? '+91 84597-00000'); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="text-primary text-xl mt-1">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold mb-1">Email</h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($company['email'] ?? 'info@pofinfraa.com'); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="text-primary text-xl mt-1">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold mb-1">Working Hours</h3>
                                <p class="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p class="text-gray-600">Saturday: 9:00 AM - 1:00 PM</p>
                                <p class="text-gray-600">Sunday: Closed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-primary text-2xl">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary text-2xl">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary text-2xl">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary text-2xl">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Send Us a Message</h2>
                    
                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-gray-700 font-medium mb-2">Your Name</label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="John Doe">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="john@example.com">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="+1 234 567 890">
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                                <input type="text" id="subject" name="subject" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="Project Inquiry">
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Your Message</label>
                            <textarea id="message" name="message" rows="6" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                      placeholder="How can we help you?"></textarea>
                        </div>

                        <div>
                            <button type="submit" class="w-full bg-primary hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md transition-colors">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        <div class="mt-20">
            <div class="bg-white rounded-lg shadow-lg p-4">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3506.2888885646584!2d77.17147661507725!3d28.502930982472843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1e3f14b2b2d1%3A0x7c4aa0d368881c1f!2sChhatarpur%20Enclave%20Phase%202%2C%20Chhatarpur%2C%20New%20Delhi%2C%20Delhi%20110074!5e0!3m2!1sen!2sin!4v1629890183697!5m2!1sen!2sin"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
