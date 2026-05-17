<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gaming Zone - Book Your Gaming Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --accent: #6f42c1;
            --accent-hover: #5a32a3;
        }
        [data-theme="dark"] {
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --text-primary: #ffffff;
            --text-secondary: #adb5bd;
            --accent: #6f42c1;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-secondary); color: var(--text-primary); }
        
        /* Header */
        .header {
            background: var(--bg-primary);
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 10px; font-size: 24px; font-weight: 700; color: var(--accent); }
        .logo i { font-size: 28px; }
        
        /* Nav */
        .nav { display: flex; gap: 20px; align-items: center; }
        .nav a { text-decoration: none; color: var(--text-primary); font-weight: 500; transition: 0.2s; }
        .nav a:hover { color: var(--accent); }
        
        /* Theme Toggle */
        .theme-toggle { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--text-primary); }
        
        /* Hero */
        .hero { background: var(--bg-primary); padding: 80px 0; text-align: center; }
        .hero h1 { font-size: 48px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary); }
        .hero p { font-size: 18px; color: var(--text-secondary); margin-bottom: 32px; }
        .btn-primary { background: var(--accent); color: white; padding: 14px 32px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background: var(--accent-hover); }
        
        /* Features */
        .features { padding: 80px 0; }
        .section-title { text-align: center; font-size: 32px; font-weight: 700; margin-bottom: 48px; color: var(--text-primary); }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px; }
        .feature-card { background: var(--bg-primary); padding: 32px; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .feature-icon { width: 64px; height: 64px; background: rgba(111, 66, 193, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--accent); font-size: 24px; }
        .feature-card h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
        .feature-card p { color: var(--text-secondary); font-size: 14px; }
        
        /* Gaming Zones */
        .zones { padding: 80px 0; background: var(--bg-primary); }
        .zones-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
        .zone-card { background: var(--bg-secondary); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .zone-header { padding: 24px; border-bottom: 1px solid var(--border-color, #e9ecef); }
        .zone-header h3 { font-size: 18px; font-weight: 600; color: var(--text-primary); }
        .zone-header p { color: var(--text-secondary); font-size: 14px; }
        .zone-stats { padding: 16px 24px; display: flex; gap: 24px; }
        .zone-stat { text-align: center; }
        .zone-stat-value { font-size: 20px; font-weight: 700; color: var(--accent); }
        .zone-stat-label { font-size: 12px; color: var(--text-secondary); }
        
        /* CTA */
        .cta { padding: 80px 0; text-align: center; }
        .cta h2 { font-size: 32px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary); }
        .cta p { color: var(--text-secondary); margin-bottom: 32px; }
        
        /* Footer */
        .footer { background: var(--bg-primary); padding: 40px 0; text-align: center; border-top: 1px solid var(--border-color, #e9ecef); }
        .footer p { color: var(--text-secondary); font-size: 14px; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-gamepad"></i>
                    Gaming Zone
                </div>
                <nav class="nav">
                    @auth
                        <a href="{{ route('player.dashboard') }}">Dashboard</a>
                        <a href="{{ route('website.booking.create') }}">Book PC</a>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Book Your Gaming Session</h1>
            <p>Premium gaming PCs and comfortable gaming zones available 24/7</p>
            @auth
                <a href="{{ route('website.booking.create') }}" class="btn-primary">
                    <i class="fas fa-calendar-check"></i>
                    Book Now
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Book
                </a>
            @endauth
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose Gaming Zone?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-desktop"></i></div>
                    <h3>High-End PCs</h3>
                    <p>RTX graphics,144Hz monitors, and the latest hardware</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-couch"></i></div>
                    <h3>Comfortable Setup</h3>
                    <p>Ergonomic chairs and spacious gaming stations</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-wifi"></i></div>
                    <h3>High-Speed Internet</h3>
                    <p>Fiber optic connection for lag-free gaming</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h3>24/7 Availability</h3>
                    <p>Book anytime, play anytime</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gaming Zones -->
    <section class="zones">
        <div class="container">
            <h2 class="section-title">Available Gaming Zones</h2>
            <div class="zones-grid">
                @forelse($tenants as $tenant)
                <div class="zone-card">
                    <div class="zone-header">
                        <h3>{{ $tenant->name }}</h3>
                        <p>{{ $tenant->address ?? 'Location available' }}</p>
                    </div>
                    <div class="zone-stats">
                        <div class="zone-stat">
                            <div class="zone-stat-value">{{ $tenant->rooms->count() }}</div>
                            <div class="zone-stat-label">Rooms</div>
                        </div>
                        <div class="zone-stat">
                            <div class="zone-stat-value">{{ $tenant->pcs->count() }}</div>
                            <div class="zone-stat-label">PCs</div>
                        </div>
                        <div class="zone-stat">
                            <div class="zone-stat-value">{{ $tenant->pcs->where('status', 'available')->count() }}</div>
                            <div class="zone-stat-label">Available</div>
                        </div>
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: var(--text-secondary);">No gaming zones available at the moment.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Game?</h2>
            <p>Create an account and start booking your gaming sessions today!</p>
            @guest
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Get Started
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Gaming Zone. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            const currentTheme = html.getAttribute('data-theme');
            if (currentTheme === 'light') {
                html.setAttribute('data-theme', 'dark');
                icon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-theme', 'light');
                icon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.getElementById('themeIcon').className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>