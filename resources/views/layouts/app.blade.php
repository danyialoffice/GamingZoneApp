<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gaming Zone')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f4f5f7;
            --bg-card: #ffffff;
            --text-primary: #1a1a2e;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --sidebar-bg: #1e1e2f;
            --sidebar-text: #ffffff;
            --sidebar-hover: #2a2a3f;
            --sidebar-active: #6f42c1;
            --accent-primary: #6f42c1;
            --accent-secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --bg-primary: #0f0f1a;
            --bg-secondary: #1a1a2e;
            --bg-card: #1e1e2f;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: #334155;
            --sidebar-bg: #0a0a14;
            --sidebar-text: #f1f5f9;
            --sidebar-hover: #1a1a2e;
            --sidebar-active: #8b5cf6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand i { font-size: 28px; color: var(--accent-primary); }
        .sidebar-brand h3 { font-size: 18px; font-weight: 600; margin: 0; }

        .sidebar-nav { padding: 16px 0; }
        .nav-item { margin: 4px 12px; }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #ffffff;
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);
        }

        .nav-link i { width: 24px; margin-right: 12px; font-size: 16px; }

        .nav-section-title {
            padding: 20px 16px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
        }

        .main-content { margin-left: 260px; min-height: 100vh; transition: all 0.3s ease; }

        .top-navbar {
            background: var(--bg-primary);
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-left { display: flex; align-items: center; gap: 16px; }
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
        }
        .page-title { font-size: 20px; font-weight: 600; color: var(--text-primary); }
        .navbar-right { display: flex; align-items: center; gap: 12px; }

        /* Tenant Selector */
        .tenant-selector { position: relative; }
        
        .tenant-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            max-width: 180px;
        }
        
        .tenant-btn:hover { transform: scale(1.02); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }
        .tenant-btn i:first-child { font-size: 14px; }
        .tenant-current-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .tenant-btn i:last-child { font-size: 10px; margin-left: auto; }
        
        .tenant-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            min-width: 220px;
            box-shadow: var(--shadow-lg);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }
        
        .tenant-dropdown.show { display: block; }
        
        .tenant-dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .tenant-dropdown-item:hover { background: var(--bg-secondary); }
        .tenant-dropdown-item.active { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }
        .tenant-dropdown-item i:first-child { color: var(--text-secondary); margin-right: 12px; }
        .tenant-dropdown-item i:last-child { margin-left: auto; color: var(--accent-primary); }

        /* Theme Toggle */
        .theme-toggle {
            width: 40px; height: 40px;
            border-radius: 10px;
            border: none;
            background: var(--bg-secondary);
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s ease;
        }
        .theme-toggle:hover { background: var(--accent-primary); color: white; transform: scale(1.05); }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: var(--bg-secondary);
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s ease;
        }
        .notification-bell:hover { background: var(--accent-primary); color: white; transform: scale(1.05); }
        .notification-bell.has-unread { color: var(--accent-primary); }
        .notification-bell .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 600;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 360px;
            box-shadow: var(--shadow-lg);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }
        .notification-dropdown.show { display: block; }
        .notification-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .notification-dropdown-header h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        .notification-dropdown-header a {
            font-size: 12px;
            color: var(--accent-primary);
            text-decoration: none;
        }
        .notification-dropdown-header a:hover { text-decoration: underline; }
        .notification-list {
            max-height: 320px;
            overflow-y: auto;
        }
        .notification-item-dropdown {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .notification-item-dropdown:hover { background: var(--bg-secondary); }
        .notification-item-dropdown.unread { background: rgba(99, 102, 241, 0.05); }
        .notification-icon-sm {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .notification-icon-sm.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .notification-icon-sm.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .notification-icon-sm.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .notification-icon-sm.info { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        .notification-icon-sm.primary { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        .notification-icon-sm.secondary { background: rgba(107, 114, 128, 0.1); color: #6b7280; }
        .notification-item-content { flex: 1; min-width: 0; }
        .notification-item-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 2px;
        }
        .notification-item-message {
            font-size: 12px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .notification-item-time {
            font-size: 11px;
            color: var(--text-muted);
        }
        .notification-empty {
            padding: 32px 16px;
            text-align: center;
            color: var(--text-muted);
        }
        .notification-empty i { font-size: 32px; margin-bottom: 8px; opacity: 0.5; }
        .notification-empty p { font-size: 13px; margin: 0; }

        /* User Dropdown */
        .user-dropdown { position: relative; }
        .user-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .user-btn:hover { border-color: var(--accent-primary); }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-info { text-align: left; }
        .user-name { font-size: 14px; font-weight: 500; color: var(--text-primary); }
        .user-role { font-size: 12px; color: var(--text-secondary); }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%; right: 0;
            margin-top: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            min-width: 200px;
            box-shadow: var(--shadow-lg);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }
        .dropdown-menu-custom.show { display: block; }
        .dropdown-item-custom {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        .dropdown-item-custom:hover { background: var(--bg-secondary); color: var(--accent-primary); }
        .dropdown-item-custom i { width: 20px; margin-right: 12px; color: var(--text-secondary); }
        .dropdown-divider { height: 1px; background: var(--border-color); margin: 4px 0; }

        .content-wrapper { padding: 24px; }

        .card-custom {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card-custom:hover { box-shadow: var(--shadow-md); border-color: var(--accent-primary); transform: translateY(-2px); }
        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
        }
        .card-title-custom {
            font-size: 16px; font-weight: 600;
            color: var(--text-primary); margin: 0;
            display: flex; align-items: center; gap: 8px;
        }
        .card-body-custom { padding: 24px; }

        .btn-primary-custom {
            background: var(--accent-primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
        }
        .btn-primary-custom:hover { background: var(--accent-secondary); transform: translateY(-1px); }
        .btn-secondary-custom {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-secondary-custom:hover { background: var(--border-color); border-color: var(--accent-primary); }

        .stats-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: var(--accent-primary);
            transition: all 0.3s ease;
        }
        .stats-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); border-color: var(--accent-primary); }
        .stats-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }
        .stats-icon.primary { background: rgba(111, 66, 193, 0.1); color: var(--accent-primary); }
        .stats-icon.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .stats-icon.info { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .stats-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .stats-icon.danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .stats-value { font-size: 32px; font-weight: 700; color: var(--text-primary); line-height: 1; margin-bottom: 4px; }
        .stats-label { font-size: 14px; color: var(--text-secondary); font-weight: 500; }

        .table-custom { width: 100%; border-collapse: collapse; }
        .table-custom th {
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
        }
        .table-custom td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            color: var(--text-primary);
        }
        .table-custom tr { transition: all 0.2s ease; }
        .table-custom tbody tr:hover { background: var(--bg-secondary); }

        .badge-custom { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .badge-primary { background: rgba(111, 66, 193, 0.1); color: var(--accent-primary); }
        .badge-secondary { background: var(--bg-secondary); color: var(--text-secondary); }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: var(--text-primary); }
        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }
        .form-control-custom:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.15); }
        .form-select-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            background: var(--bg-primary);
            color: var(--text-primary);
            cursor: pointer;
        }

        .alert-success-custom {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error-custom {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
        }

        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .sidebar-overlay.show { display: block; }

        .breadcrumb-custom { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 16px; }
        .breadcrumb-custom a { color: var(--text-secondary); text-decoration: none; transition: color 0.2s ease; }
        .breadcrumb-custom a:hover { color: var(--accent-primary); }
        .breadcrumb-custom span { color: var(--text-muted); }
        .breadcrumb-custom i { font-size: 10px; color: var(--text-muted); }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            padding: 20px;
        }
        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 48px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
        }
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .auth-logo i { font-size: 48px; color: var(--accent-primary); margin-bottom: 16px; }
        .auth-logo h1 { font-size: 24px; font-weight: 600; color: var(--text-primary); }
        .auth-logo p { font-size: 14px; color: var(--text-secondary); }

        .text-primary { color: var(--text-primary) !important; }
        .text-secondary { color: var(--text-secondary) !important; }
        .text-muted-custom { color: var(--text-muted) !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: 8px !important; }
        .mb-3 { margin-bottom: 12px !important; }
        .mb-4 { margin-bottom: 16px !important; }
        .mt-4 { margin-top: 16px !important; }
        .me-2 { margin-right: 8px !important; }
        .ms-3 { margin-left: 12px !important; }
        .gap-2 { gap: 8px !important; }
        
        /* Grid System */
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .container-fluid { width: 100%; padding: 0 15px; }
        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
        .row > * { padding: 0 15px; }
        .col-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
        .col-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
        .col-3 { flex: 0 0 25%; max-width: 25%; }
        .col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
        .col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
        .col-6 { flex: 0 0 50%; max-width: 50%; }
        .col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
        .col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
        .col-9 { flex: 0 0 75%; max-width: 75%; }
        .col-10 { flex: 0 0 83.333333%; max-width: 83.333333%; }
        .col-11 { flex: 0 0 91.666667%; max-width: 91.666667%; }
        .col-12 { flex: 0 0 100%; max-width: 100%; }
        .col { flex: 1 0 0%; }
        
        @media (min-width: 576px) {
            .col-sm-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-sm-3 { flex: 0 0 25%; max-width: 25%; }
            .col-sm-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-sm-6 { flex: 0 0 50%; max-width: 50%; }
            .col-sm-12 { flex: 0 0 100%; max-width: 100%; }
        }
        
        @media (min-width: 768px) {
            .col-md-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-md-3 { flex: 0 0 25%; max-width: 25%; }
            .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-md-6 { flex: 0 0 50%; max-width: 50%; }
            .col-md-12 { flex: 0 0 100%; max-width: 100%; }
        }
        
        @media (min-width: 992px) {
            .col-lg-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-lg-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-lg-3 { flex: 0 0 25%; max-width: 25%; }
            .col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-lg-6 { flex: 0 0 50%; max-width: 50%; }
            .col-lg-12 { flex: 0 0 100%; max-width: 100%; }
        }
        
        @media (min-width: 1200px) {
            .col-xl-1 { flex: 0 0 8.333333%; max-width: 8.333333%; }
            .col-xl-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-xl-3 { flex: 0 0 25%; max-width: 25%; }
            .col-xl-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-xl-6 { flex: 0 0 50%; max-width: 50%; }
            .col-xl-12 { flex: 0 0 100%; max-width: 100%; }
        }
    </style>
    @yield('extra_styles')
</head>
<body>
    @auth
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-gamepad"></i>
                <h3>Gaming Zone</h3>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section-title">Main</div>
                
                @if(auth()->user()->isSuperAdmin())
                    <div class="nav-item">
                        <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->is('super-admin/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-crown"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('super-admin.tenants.index') }}" class="nav-link {{ request()->is('super-admin/tenants*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Tenants</span>
                        </a>
                    </div>
                @elseif(auth()->user()->isPlayer())
                    <div class="nav-item">
                        <a href="{{ route('player.dashboard') }}" class="nav-link {{ request()->is('player/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('website.booking.create') }}" class="nav-link {{ request()->is('booking/create') ? 'active' : '' }}">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Book PC</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('website.booking.my-bookings') }}" class="nav-link {{ request()->is('booking/my-bookings') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>My Bookings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('website.booking.pc-status') }}" class="nav-link {{ request()->is('booking/pc-status') ? 'active' : '' }}">
                            <i class="fas fa-tv"></i>
                            <span>PC Status</span>
                        </a>
                    </div>
                @else
                    <div class="nav-item">
                        <a href="{{ route('tenant.dashboard') }}" class="nav-link {{ request()->is('tenant/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('tenant.rooms.index') }}" class="nav-link {{ request()->is('tenant/rooms*') ? 'active' : '' }}">
                            <i class="fas fa-door-open"></i>
                            <span>Rooms</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('tenant.pcs.index') }}" class="nav-link {{ request()->is('tenant/pcs*') ? 'active' : '' }}">
                            <i class="fas fa-desktop"></i>
                            <span>PCs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('tenant.bookings.index') }}" class="nav-link {{ request()->is('tenant/bookings*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                    </div>
                @endif

                @if(!auth()->user()->isPlayer())
                    <div class="nav-section-title">Actions</div>
                    
                    <div class="nav-item">
                        <a href="{{ route('website.booking.pc-status') }}" class="nav-link {{ request()->is('booking/pc-status') ? 'active' : '' }}">
                            <i class="fas fa-tv"></i>
                            <span>Book PC</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('website.booking.my-bookings') }}" class="nav-link {{ request()->is('booking/my-bookings') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>My Bookings</span>
                        </a>
                    </div>
                @endif

                <div class="nav-section-title">Account</div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <header class="top-navbar">
                <div class="navbar-left">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
                </div>
                
                <div class="navbar-right">
                    @if(auth()->user()->tenantUsers()->count() > 1 || (\App\Models\Tenant::current() && auth()->user()->tenantUsers()->count() >= 1))
                        <div class="tenant-selector">
                            <button class="tenant-btn" onclick="toggleTenantDropdown()">
                                <i class="fas fa-building"></i>
                                <span class="tenant-current-name">{{\App\Models\Tenant::current()?->name ?? 'Select Zone'}}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="tenant-dropdown" id="tenantDropdown">
                                @foreach(auth()->user()->tenantUsers()->with('tenant')->get() as $tu)
                                    <a href="{{ route('set-tenant', $tu->tenant_id) }}" class="tenant-dropdown-item {{ \App\Models\Tenant::current()?->id == $tu->tenant_id ? 'active' : '' }}">
                                        <i class="fas fa-gamepad"></i>
                                        <span>{{ $tu->tenant->name }}</span>
                                        @if(\App\Models\Tenant::current()?->id == $tu->tenant_id)
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Notification Bell -->
                    <div class="notification-wrapper" style="position: relative;">
                        <button class="notification-bell" id="notificationBell" onclick="toggleNotificationDropdown()" title="Notifications">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-dropdown-header">
                                <h4>Notifications</h4>
                                <a href="{{ route('notifications.index') }}">View All</a>
                            </div>
                            <div class="notification-list" id="notificationList">
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>Loading notifications...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Theme">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                    
                    <div class="user-dropdown">
                        <button class="user-btn" onclick="toggleUserDropdown()">
                            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-role">
                                    @if(auth()->user()->isSuperAdmin())
                                        Super Admin
                                    @else
                                        {{ auth()->user()->isTenantAdmin() ? 'Tenant Admin' : (auth()->user()->isBookingManager() ? 'Booking Manager' : 'Player') }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-chevron-down" style="color: var(--text-muted); font-size: 12px;"></i>
                        </button>
                        
                        <div class="dropdown-menu-custom" id="userDropdown">
                            <a href="#" class="dropdown-item-custom">
                                <i class="fas fa-user"></i>
                                Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item-custom" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert-success-custom">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert-error-custom">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    @else
        @yield('content')
    @endauth

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

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        function toggleUserDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        function toggleTenantDropdown() {
            document.getElementById('tenantDropdown').classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown) dropdown.classList.remove('show');
            }
            if (!e.target.closest('.tenant-selector')) {
                const dropdown = document.getElementById('tenantDropdown');
                if (dropdown) dropdown.classList.remove('show');
            }
            if (!e.target.closest('.notification-wrapper')) {
                const dropdown = document.getElementById('notificationDropdown');
                if (dropdown) dropdown.classList.remove('show');
            }
        });

        // Notification functions
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                loadNotifications();
            }
        }

        function loadNotifications() {
            fetch('/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                    renderNotificationList(data.notifications);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notificationList').innerHTML = `
                        <div class="notification-empty">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Failed to load notifications</p>
                        </div>
                    `;
                });
        }

        function updateNotificationBadge(count) {
            const bell = document.getElementById('notificationBell');
            let badge = bell.querySelector('.badge');
            
            if (count > 0) {
                bell.classList.add('has-unread');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge';
                    bell.appendChild(badge);
                }
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                bell.classList.remove('has-unread');
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        }

        function renderNotificationList(notifications) {
            const list = document.getElementById('notificationList');
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="notification-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }

            list.innerHTML = notifications.map(n => `
                <div class="notification-item-dropdown ${n.is_read ? '' : 'unread'}" 
                     onclick="handleNotificationClick('${n.type}', ${JSON.stringify(n.data).replace(/"/g, '"')})">
                    <div class="notification-icon-sm ${n.color}">
                        <i class="fas ${n.icon}"></i>
                    </div>
                    <div class="notification-item-content">
                        <div class="notification-item-title">${n.title}</div>
                        <div class="notification-item-message">${n.message}</div>
                        <div class="notification-item-time">${n.time_ago}</div>
                    </div>
                </div>
            `).join('');
        }

        function handleNotificationClick(type, data) {
            // Mark as read and redirect
            if (data && data.booking_id) {
                window.location.href = '/booking/' + data.booking_id;
            } else if (type === 'booking_created' || type === 'booking_approved' || 
                       type === 'booking_rejected' || type === 'booking_cancelled') {
                window.location.href = '/booking/my-bookings';
            } else if (type.startsWith('payment')) {
                window.location.href = '/booking/my-bookings';
            } else {
                window.location.href = '/notifications';
            }
        }

        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => updateNotificationBadge(data.count))
                .catch(() => {});
        });
    </script>

    @yield('scripts')
</body>
</html>
