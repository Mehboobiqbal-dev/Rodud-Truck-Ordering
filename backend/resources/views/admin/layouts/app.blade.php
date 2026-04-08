<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Rodud Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        slate: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-primary: #06060a;
            --bg-secondary: #0d0d14;
            --bg-card: #12121c;
            --bg-card-hover: #181826;
            --bg-input: #1a1a2e;
            --border-color: rgba(255,255,255,0.06);
            --border-hover: rgba(255,255,255,0.12);
            --text-primary: #f0f0f5;
            --text-secondary: #8b8ba3;
            --text-muted: #5a5a72;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --accent-glow: rgba(99,102,241,0.25);
            --success: #22c55e;
            --success-bg: rgba(34,197,94,0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245,158,11,0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239,68,68,0.12);
            --info: #3b82f6;
            --info-bg: rgba(59,130,246,0.12);
            --sidebar-width: 260px;
            --header-height: 64px;
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: var(--header-height);
        }

        .sidebar-header img {
            height: 32px;
            width: auto;
            filter: invert(1);
        }

        .sidebar-brand {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.3px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section {
            margin-top: 20px;
            margin-bottom: 8px;
            padding: 0 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--bg-card);
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(99,102,241,0.08);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: var(--text-muted); }

        /* ========== HEADER ========== */
        .header {
            position: fixed;
            top: 0; left: var(--sidebar-width); right: 0;
            height: var(--header-height);
            background: rgba(6,6,10,0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 90;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-title {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn {
            position: relative;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-color);
            width: 38px; height: 38px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-btn:hover {
            color: var(--text-primary);
            background: rgba(255,255,255,0.1);
        }

        .notification-indicator {
            position: absolute;
            top: -4px; right: -4px;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-secondary);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-logout:hover {
            color: var(--danger);
            border-color: rgba(239,68,68,0.3);
            background: var(--danger-bg);
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 32px;
            min-height: calc(100vh - var(--header-height));
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* ========== CARDS ========== */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            border-color: var(--border-hover);
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.2px;
        }

        .card-body { padding: 24px; }

        /* ========== STAT CARDS ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-hover);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
        }

        .stat-card.accent::after { background: linear-gradient(90deg, var(--accent), #a78bfa); }
        .stat-card.warning::after { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card.info::after { background: linear-gradient(90deg, var(--info), #60a5fa); }
        .stat-card.success::after { background: linear-gradient(90deg, var(--success), #4ade80); }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            margin-bottom: 16px;
        }

        .stat-card.accent .stat-icon { background: rgba(99,102,241,0.12); color: var(--accent); }
        .stat-card.warning .stat-icon { background: var(--warning-bg); color: var(--warning); }
        .stat-card.info .stat-icon { background: var(--info-bg); color: var(--info); }
        .stat-card.success .stat-icon { background: var(--success-bg); color: var(--success); }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ========== TABLE ========== */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        tbody td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
            vertical-align: middle;
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--bg-card-hover);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-cell .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-cell .name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        .user-cell .email {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ========== BADGES ========== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
        }

        .badge-pending { background: var(--warning-bg); color: var(--warning); }
        .badge-pending::before { background: var(--warning); }

        .badge-in_progress, .badge-in-progress { background: var(--info-bg); color: var(--info); }
        .badge-in_progress::before, .badge-in-progress::before { background: var(--info); }

        .badge-delivered { background: var(--success-bg); color: var(--success); }
        .badge-delivered::before { background: var(--success); }

        .badge-active { background: var(--success-bg); color: var(--success); }
        .badge-active::before { background: var(--success); }

        .badge-blocked { background: var(--danger-bg); color: var(--danger); }
        .badge-blocked::before { background: var(--danger); }

        /* ========== BUTTONS ========== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 16px var(--accent-glow);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-secondary);
            border-color: var(--border-color);
        }

        .btn-secondary:hover {
            color: var(--text-primary);
            border-color: var(--border-hover);
            background: var(--bg-card-hover);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: rgba(239,68,68,0.2);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #fff;
        }

        .btn-success {
            background: var(--success-bg);
            color: var(--success);
            border-color: rgba(34,197,94,0.2);
        }

        .btn-success:hover {
            background: var(--success);
            color: #fff;
        }

        .btn-warning {
            background: var(--warning-bg);
            color: var(--warning);
            border-color: rgba(245,158,11,0.2);
        }

        .btn-warning:hover {
            background: var(--warning);
            color: #fff;
        }

        .btn-info {
            background: var(--info-bg);
            color: var(--info);
            border-color: rgba(59,130,246,0.2);
        }

        .btn-info:hover {
            background: var(--info);
            color: #fff;
        }

        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ========== FORMS ========== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8ba3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        /* ========== FILTERS ========== */
        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-input {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: var(--transition);
            min-width: 220px;
        }

        .filter-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .filter-select {
            padding: 8px 36px 8px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%238b8ba3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: var(--accent);
        }

        /* ========== ALERTS ========== */
        .alert {
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid rgba(34,197,94,0.2);
        }

        .alert-error {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(239,68,68,0.2);
        }

        /* ========== PAGINATION ========== */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }

        .pagination a {
            color: var(--text-secondary);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .pagination a:hover {
            color: var(--text-primary);
            border-color: var(--accent);
            background: rgba(99,102,241,0.08);
        }

        .pagination .active span {
            color: #fff;
            background: var(--accent);
            border: 1px solid var(--accent);
        }

        .pagination .disabled span {
            color: var(--text-muted);
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            cursor: not-allowed;
        }

        /* ========== DETAIL GRID ========== */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .detail-item label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 6px;
        }

        .detail-item .value {
            font-size: 15px;
            font-weight: 500;
            color: var(--text-primary);
        }

        /* ========== FOOTER ========== */
        .footer {
            padding: 24px 32px;
            margin-left: var(--sidebar-width);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-logo img {
            height: 28px;
            width: auto;
            opacity: 0.7;
            filter: invert(1);
        }

        .footer-text {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ========== STATUS-FORM (inline) ========== */
        .status-form {
            display: inline-flex;
            gap: 6px;
        }

        .status-form select {
            padding: 4px 28px 4px 8px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 12px;
            font-family: inherit;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%238b8ba3' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            cursor: pointer;
            outline: none;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .header, .main-content, .footer {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/images/header_logo.png" alt="Rodud">
            <span class="sidebar-brand">Rodud Admin</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </a>

            <div class="nav-section">Management</div>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-truck-fast"></i>
                Orders
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Users
            </a>
            <a href="{{ route('admin.messages') }}" class="nav-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                Messages
                @php
                    $unreadMessages = \App\Models\Message::where('sender_type', 'user')->whereNull('read_at')->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="notification-badge">{{ $unreadMessages }}</span>
                @endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <h2 class="header-title">@yield('header-title', 'Dashboard')</h2>
        </div>
        <div class="header-right">
            <!-- Notifications Bell -->
            <a href="{{ route('admin.notifications') }}" class="notification-btn" title="View Notifications">
                <i class="far fa-bell"></i>
                @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                    <span class="notification-indicator">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>

            <!-- Logout -->
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-logo">
            <img src="/images/RodudLogo.png" alt="Rodud">
            <span class="footer-text">Rodud Truck Ordering System</span>
        </div>
        <span class="footer-text">&copy; {{ date('Y') }} Rodud. All rights reserved.</span>
    </div>

    @yield('scripts')
</body>
</html>
