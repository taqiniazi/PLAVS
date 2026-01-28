<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLAVS  | Physical Library Assets Visibility System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <!-- AOS Animation Library -->
    <link href="{{ asset('css/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
    <!-- Custom CSS -->

</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stats">Stats</a>
                    </li>
                    @auth
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-primary me-2" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="floating-element floating-1"></div>
        <div class="floating-element floating-2"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="hero-title">Transform Your Library Experience</h1>
                    <p class="hero-subtitle">PLAVS <em class="text-orange">(Physical Library Assets Visibility System)</em> revolutionizes how you manage, share, and enjoy your book collection. Keep a digital inventory, track lending, and connect with fellow book lovers.</p>
                    <div class="hero-buttons">
                        <a href="#features" class="btn btn-primary me-3">Explore Features</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light">Get Started</a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5 hero-image text-center" data-aos="fade-left" data-aos-duration="1000">
                    <img src="{{ asset('images/landing-image.jpeg') }}" alt="Library Management Dashboard" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center" data-aos="fade-up" data-aos-duration="1000">
                    <h2 class="section-title">Powerful Features</h2>
                    <p class="section-subtitle">Everything you need to manage your personal library efficiently and beautifully</p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-collection"></i>
                        </div>
                        <h4>Digital Book Inventory</h4>
                        <p>Keep a complete digital record of all your books. Organize them by shelves, categories, and reading status.</p>
                        <ul class="feature-list">
                            <li><i class="bi bi-check-circle"></i> Scan ISBN to add books instantly</li>
                            <li><i class="bi bi-check-circle"></i> Add custom tags and personal notes</li>
                            <li><i class="bi bi-check-circle"></i> Track reading progress and reviews</li>
                            <li><i class="bi bi-check-circle"></i> Visual organization with custom covers</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <h4>Smart Lending Management</h4>
                        <p>Easily manage lending to friends and track borrowed books with automated reminders.</p>
                        <ul class="feature-list">
                            <li><i class="bi bi-check-circle"></i> Set due dates and get notifications</li>
                            <li><i class="bi bi-check-circle"></i> Track complete borrowing history</li>
                            <li><i class="bi bi-check-circle"></i> Send automatic return reminders</li>
                            <li><i class="bi bi-check-circle"></i> Rate borrowers and add notes</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>Community Libraries</h4>
                        <p>Connect with other book owners, create shared libraries, and manage memberships efficiently.</p>
                        <ul class="feature-list">
                            <li><i class="bi bi-check-circle"></i> Create private or public libraries</li>
                            <li><i class="bi bi-check-circle"></i> Manage member roles and permissions</li>
                            <li><i class="bi bi-check-circle"></i> Share recommendations and reviews</li>
                            <li><i class="bi bi-check-circle"></i> Organize virtual book clubs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center" data-aos="fade-up" data-aos-duration="1000">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Get started with PLAVS in three simple steps</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Sign Up & Add Books</h4>
                        <p>Create your account and start building your digital library. Add books manually or scan ISBN barcodes for quick addition. Import your existing collection from spreadsheets or other apps.</p>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Organize & Categorize</h4>
                        <p>Organize your books by custom shelves, genres, authors, or reading status. Create a system that works perfectly for your collection. Add tags, ratings, and personal notes.</p>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Share & Connect</h4>
                        <p>Lend books to friends, join community libraries, and connect with other readers. Manage everything from your beautiful dashboard. Get insights about your reading habits.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0" data-aos="fade-up" data-aos-duration="1000">
                    <div class="stat-item">
                        <h3>{{ number_format($booksCount ?? 0) }}+</h3>
                        <p>Books Managed</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="stat-item">
                        <h3>{{ number_format($usersCount ?? 0) }}+</h3>
                        <p>Happy Users</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="stat-item">
                        <h3>{{ number_format($lentBooksCount ?? 0) }}+</h3>
                        <p>Books Lent</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                    <div class="stat-item">
                        <h3>{{ number_format($librariesCount ?? 0) }}+</h3>
                        <p>Community Libraries</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Section -->
    <section class="admin-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="zoom-in" data-aos-duration="1000">
                    <div class="admin-card">
                        <h2>Access Your Advanced Admin Panel</h2>
                        <p>Already have an account? Access the full admin panel to manage your library, users, and settings with powerful analytics and control features.</p>
                        
                        <div class="admin-features">
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> Full library management controls</div>
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> Advanced analytics and reports</div>
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> User and member management</div>
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> Notification and reminder settings</div>
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> Security and privacy controls</div>
                            <div class="admin-feature"><i class="bi bi-check-circle"></i> Custom branding options</div>
                        </div>
                        
                        <a href="{{ route('login') }}" target="_blank" class="d-inline-block btn btn-primary p-3 rounded-pill">
                            <i class="bi bi-speedometer2 me-2"></i>Go to Admin Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <!-- <div class="footer-logo">PLAVS </div> -->
                    <div class="footer-logo"><img src="{{ asset('images/footer-logo.png') }}" alt="Logo" class="logo"></div>

                    <p class="footer-about">PLAVS  is your complete digital solution for organizing, tracking, and sharing your personal book collection. Join thousands of book lovers who have transformed their libraries.</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-5 mb-lg-0">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#stats">Stats</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4 mb-5 mb-lg-0">
                    <h5 class="footer-heading">Features</h5>
                    <ul class="footer-links">
                        <li><a href="#">Digital Inventory</a></li>
                        <li><a href="#">Lending Management</a></li>
                        <li><a href="#">Community Libraries</a></li>
                        <li><a href="#">Admin Dashboard</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-heading">Get Started</h5>
                    <p class="footer-about">Ready to transform your library management experience?</p>
                    <a href="{{ route('login') }}" target="_blank" class="btn btn-primary">Access Admin Panel</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 PLAVS . All rights reserved. | Designed with <i class="bi bi-heart-fill text-danger"></i> for book lovers everywhere</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- AOS Animation Library -->
    <script src="{{ asset('js/aos.js') }}"></script>
    <!-- Custom JS -->
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                    
                    // Update active nav link
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });
        
        // Animate stats counter
        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                element.innerHTML = value.toLocaleString() + '+';
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
        
        // Trigger stats animation when in view
        const statsSection = document.getElementById('stats');
        const statValues = document.querySelectorAll('.stat-item h3');
        let statsAnimated = false;
        
        function checkStatsInView() {
            const rect = statsSection.getBoundingClientRect();
            const inView = rect.top <= window.innerHeight && rect.bottom >= 0;
            
            if (inView && !statsAnimated) {
                statsAnimated = true;
                animateCounter(statValues[0], 0, {{ $booksCount ?? 0 }}, 1500);
                animateCounter(statValues[1], 0, {{ $usersCount ?? 0 }}, 1500);
                animateCounter(statValues[2], 0, {{ $lentBooksCount ?? 0 }}, 1500);
                animateCounter(statValues[3], 0, {{ $librariesCount ?? 0 }}, 1500);
            }
        }
        
        window.addEventListener('scroll', checkStatsInView);
        window.addEventListener('load', checkStatsInView);
    </script>
</body>
</html>