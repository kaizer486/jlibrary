<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - @yield('title', 'Library')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'playfair': ['Playfair Display', 'serif'],
                    },
                    colors: {
                        'jlibrary': {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
    /* ========================================== */
    /* BODY BACKGROUND                           */
    /* ========================================== */
    body {
        background: #f0f2f5;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    /* ========================================== */
    /* LIBRARY BACKGROUND - Glass                */
    /* ========================================== */
    .library-bg {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        background-attachment: fixed;
        position: relative;
        min-height: 100vh;
    }

    .library-bg .content-wrapper {
        position: relative;
        z-index: 1;
        min-height: 100vh;
    }

    /* ========================================== */
    /* TOPBAR - Glass with Orange Border         */
    /* ========================================== */
    .library-topbar {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 2px solid #EA580C;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.15);
        padding: 12px 24px;
        position: sticky;
        top: 0;
        z-index: 50;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .library-topbar .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .library-topbar .logo img {
        border: none;
    }

    .library-topbar .logo span {
        font-size: 1.25rem;
        font-weight: 700;
        font-family: 'Playfair Display', serif;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #f97416ce, #bb541dd5, #c2400ce7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .library-topbar .nav-links {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .library-topbar .nav-links a {
        color: #4b5563;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        padding: 6px 4px;
        border-bottom: 2px solid transparent;
        font-weight: 500;
    }

    .library-topbar .nav-links a:hover {
        color: #EA580C;
        border-bottom-color: #EA580C;
    }

    .library-topbar .nav-links .text-white {
        color: #1f2937 !important;
        font-weight: 600;
    }

    .library-topbar .nav-links .text-white i {
        color: #EA580C;
        font-weight: 700;
    }

    .library-topbar .nav-links .btn-library-nav {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white !important;
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.8rem;
        border-bottom: none !important;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
        -webkit-text-fill-color: white !important;
    }

    .library-topbar .nav-links .btn-library-nav:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.4);
        color: white !important;
    }

    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        color: #4b5563;
        font-size: 1.5rem;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .library-topbar .nav-links {
            display: none;
        }
        .mobile-menu-btn {
            display: block;
        }
        .library-topbar {
            padding: 12px 16px;
        }
    }

    /* ========================================== */
    /* FULL-BLEED PAGE                           */
    /* ========================================== */
    .content-wrapper > .container {
        max-width: 100% !important;
        padding-left: 32px;
        padding-right: 32px;
        margin: 0 !important;
    }

    @media (max-width: 768px) {
        .content-wrapper > .container {
            padding-left: 16px;
            padding-right: 16px;
        }
    }

    /* ========================================== */
    /* HERO - Soft Orange Gradient               */
    /* ========================================== */
    .library-hero {
        background: linear-gradient(135deg, #e94d10f6 0%, #dd5a13f1 70%, #d35b2cfd 100%);
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(249, 115, 22, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .library-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #FB923C, #FBBF24);
        z-index: 2;
    }

    .library-hero .hero-content {
        position: relative;
        z-index: 1;
        padding: 44px 48px 36px;
    }

    .library-hero .hero-content h1 {
        color: #ffffff !important;
        font-weight: 800;
        text-shadow: 0 2px 20px rgba(0, 0, 0, 0.15);
    }

    .library-hero .hero-content p {
        color: rgba(255, 255, 255, 0.92) !important;
        font-size: 1.1rem;
        text-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
    }

    .library-hero .hero-content .text-white {
        color: #ffffff !important;
    }

    .library-hero .hero-content .btn-library {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 700;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
        text-shadow: 0 1px 8px rgba(0, 0, 0, 0.15);
    }

    .library-hero .hero-content .btn-library:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.4);
        background: linear-gradient(135deg, #FB923C, #EA580C);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    .library-hero .hero-content .text-orange {
        color: #F97316;
        font-weight: 700;
    }

    @media (max-width: 640px) {
        .library-hero .hero-content {
            padding: 28px 24px;
        }
        .library-hero .hero-content h1 {
            font-size: 1.8rem;
        }
    }

    /* ========================================== */
    /* BOOKSHELF RACK - Glass with Orange Border */
    /* ========================================== */
    .bookshelf-rack {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 2px solid rgba(234, 88, 12, 0.25);
        border-radius: 18px;
        padding: 28px 24px 8px;
        position: relative;
        box-shadow: 0 2px 12px rgba(234, 88, 12, 0.08);
    }

    .shelf-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0;
        border-bottom: 3px solid rgba(234, 88, 12, 0.25);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 36px;
        position: relative;
    }

    .shelf-row::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -6px;
        height: 6px;
        background: linear-gradient(90deg, rgba(249, 115, 22, 0.25), rgba(194, 65, 12, 0.25));
        border-radius: 0 0 4px 4px;
    }

    .shelf-bay {
        height: 260px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        border-right: 2px solid rgba(234, 88, 12, 0.12);
        padding: 0 14px 10px;
        text-decoration: none;
        color: #1f2937;
        font-weight: bold;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .shelf-bay:last-child {
        border-right: none;
    }

    .shelf-bay:hover {
        background: rgba(249, 115, 22, 0.06);
        border-radius: 8px;
    }

    .shelf-bay-label {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .shelf-bay-code {
        font-weight: 700;
        font-size: 1rem;
        background: linear-gradient(135deg, #F97316, #EA580C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .shelf-bay-count {
        font-size: 0.65rem;
        color: #9ca3af;
        font-weight: 500;
    }

    .shelf-bay-name {
        font-size: 0.72rem;
        color: #4b5563;
        margin-bottom: 10px;
        font-weight: 600;
    }

    /* ========================================== */
    /* BOOK SPINES - With Individual Borders     */
    /* ========================================== */
    .book-spines-container {
        height: 140px;
        display: flex;
        align-items: flex-end;
        gap: 5px;
        overflow: hidden;
        margin-top: auto;
        padding-bottom: 4px;
        perspective: 800px;
        padding: 2px 2px 0 2px;
    }

    .spine-book-cover {
        flex: 0 0 20px !important;
        width: 20px !important;
        height: 130px;
        border-radius: 2px 2px 0 0;
        overflow: hidden;
        border: 1.5px solid rgba(234, 88, 12, 0.2);
        box-shadow: 
            inset 1px 0 0 rgba(255, 255, 255, 0.4),
            inset -1px 0 0 rgba(0, 0, 0, 0.05),
            0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: block;
        text-decoration: none;
        position: relative;
        transform-style: preserve-3d;
        cursor: pointer;
        transform-origin: bottom center;
    }

    .spine-book-cover:hover {
        height: 140px !important;
        width: 20px !important;
        flex: 0 0 20px !important;
        z-index: 20;
        border-radius: 4px 4px 0 0;
        border: 2px solid rgba(249, 115, 22, 0.5);
        box-shadow: 
            0 20px 50px rgba(0, 0, 0, 0.15),
            0 0 30px rgba(249, 115, 22, 0.15),
            inset 1px 0 0 rgba(255, 255, 255, 0.4),
            inset -1px 0 0 rgba(0, 0, 0, 0.1);
    }

    .spine-book-cover:hover .spine-book-img {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 2s ease;
    }

    .spine-book-cover::before {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 10%;
        width: 80%;
        height: 16px;
        background: radial-gradient(ellipse, rgba(0, 0, 0, 0.1), transparent);
        border-radius: 50%;
        filter: blur(10px);
        opacity: 0;
        transition: opacity 2s ease;
        pointer-events: none;
    }

    .spine-book-cover:hover::before {
        opacity: 1;
    }

    .spine-book-cover::after {
        content: '';
        position: absolute;
        top: 0;
        left: 20%;
        width: 20%;
        height: 50%;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.4), transparent);
        border-radius: 2px;
        pointer-events: none;
        transition: opacity 2s ease;
    }

    .spine-book-cover:hover::after {
        opacity: 0.6;
    }

    .spine-book-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 2s ease;
    }

    .spine-fallback-cover {
        width: 100%;
        height: 100%;
        background: linear-gradient(160deg, #f5f0eb, #e8e0d8);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
        transition: all 2s ease;
        border: 1px solid rgba(234, 88, 12, 0.1);
    }

    .spine-fallback-cover span {
        color: rgba(0, 0, 0, 0.5);
        font-size: 0.45rem;
        font-weight: 700;
        line-height: 1.1;
        text-align: center;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transition: font-size 2s ease;
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .spine-book-cover:hover .spine-fallback-cover {
        background: linear-gradient(160deg, #e8e0d8, #d5ccc4);
        border-color: rgba(249, 115, 22, 0.3);
    }

    .spine-book-cover:hover .spine-fallback-cover span {
        font-size: 0.6rem;
        color: rgba(0, 0, 0, 0.6);
        -webkit-text-fill-color: transparent;
    }

    .spine-overflow-cover {
        flex: 0 0 20px;
        width: 20px;
        height: 110px;
        border-radius: 2px;
        background: rgba(255, 255, 255, 0.7);
        border: 1.5px dashed rgba(234, 88, 12, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(0, 0, 0, 0.2);
        font-size: 0.55rem;
        font-weight: 600;
        transition: all 2s ease;
    }

    .spine-overflow-cover:hover {
        height: 140px;
        background: rgba(255, 255, 255, 0.85);
        border-color: rgba(249, 115, 22, 0.4);
        color: rgba(0, 0, 0, 0.4);
        transform: translateZ(30px);
    }

    .empty-shelf-text {
        color: rgba(0, 0, 0, 0.2);
        font-size: 0.6rem;
        align-self: center;
        padding-bottom: 10px;
        font-weight: 500;
    }

    /* ========================================== */
    /* SHELF CAPACITY                            */
    /* ========================================== */
    .shelf-capacity {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
    }

    .shelf-capacity .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #34d399;
        flex-shrink: 0;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .shelf-capacity .bar {
        flex: 1;
        height: 4px;
        background: rgba(234, 88, 12, 0.1);
        border-radius: 99px;
        overflow: hidden;
        border: 1px solid rgba(234, 88, 12, 0.05);
    }

    .shelf-capacity .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #34d399, #10b981);
    }

    .shelf-capacity-label {
        font-size: 0.6rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* ========================================== */
    /* SEARCH BAR - Glass with Orange Border     */
    /* ========================================== */
    .search-bar {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 2px solid rgba(234, 88, 12, 0.2);
        border-radius: 12px;
        padding: 12px 18px;
        color: #1f2937;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 4px rgba(234, 88, 12, 0.05);
        font-weight: 500;
    }

    .search-bar::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .search-bar:focus {
        border-color: #EA580C;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        outline: none;
        background: rgba(255, 255, 255, 0.95);
    }

    .search-bar option {
        color: #1f2937;
        background: #ffffff;
    }

    /* ========================================== */
    /* BROWSE TABS - Glass with Orange Border    */
    /* ========================================== */
    .browse-tab {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 1.5px solid rgba(234, 88, 12, 0.15);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .browse-tab:hover {
        color: #1f2937;
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(249, 115, 22, 0.3);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.08);
    }

    .browse-tab-active {
        color: #EA580C;
        background: rgba(249, 115, 22, 0.08);
        border-color: rgba(249, 115, 22, 0.35);
        border-width: 2px;
    }

    .browse-tab i {
        color: #EA580C;
        font-size: 0.9rem;
    }

    /* ========================================== */
    /* BUTTONS - Orange                          */
    /* ========================================== */
    .btn-library {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 700;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
        text-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-library:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.4);
        background: linear-gradient(135deg, #FB923C, #EA580C);
        border-color: rgba(255, 255, 255, 0.35);
        color: white;
        text-decoration: none;
    }

    .btn-library i {
        color: rgba(255, 255, 255, 0.9);
    }

    .btn-library-outline {
        background: transparent;
        color: #EA580C;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid rgba(249, 115, 22, 0.35);
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-library-outline:hover {
        background: rgba(249, 115, 22, 0.08);
        border-color: #EA580C;
        color: #C2410C;
        text-decoration: none;
        box-shadow: 0 2px 12px rgba(249, 115, 22, 0.15);
    }

    /* ========================================== */
    /* NOTIFICATIONS - Glass with Orange Border  */
    /* ========================================== */
    .notification-bell {
        position: relative;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
        cursor: pointer;
        color: #6b7280;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 2px solid rgba(234, 88, 12, 0.15);
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.95);
        color: #EA580C;
        border-color: rgba(249, 115, 22, 0.35);
        transform: scale(1.05);
    }

    .notification-bell .badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        background: #ef4444;
        color: white;
        font-size: 0.6rem;
        font-weight: 700;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .notification-bell .badge.hidden {
        display: none;
    }

    .notifications-dropdown {
        position: absolute;
        right: 0;
        top: calc(100% + 10px);
        width: 380px;
        max-height: 420px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 2px solid rgba(234, 88, 12, 0.2);
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        z-index: 100;
        overflow: hidden;
        display: none;
    }

    .notifications-dropdown.open {
        display: block;
    }

    .notifications-dropdown .dropdown-header {
        padding: 12px 16px;
        border-bottom: 2px solid rgba(234, 88, 12, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.5);
    }

    .notifications-dropdown .dropdown-header h4 {
        font-weight: 700;
        color: #1f2937;
        font-size: 0.85rem;
    }

    .notifications-dropdown .dropdown-header h4 i {
        color: #EA580C;
    }

    .notifications-dropdown .dropdown-header a {
        font-size: 0.7rem;
        color: #EA580C;
        transition: color 0.3s ease;
        text-decoration: none;
        font-weight: 600;
    }

    .notifications-dropdown .dropdown-header a:hover {
        color: #C2410C;
    }

    .notifications-dropdown .dropdown-body {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(234, 88, 12, 0.08);
        transition: background 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }

    .notification-item:hover {
        background: rgba(249, 115, 22, 0.06);
    }

    .notification-item.unread {
        background: rgba(249, 115, 22, 0.06);
        border-left: 3px solid #EA580C;
    }

    .notification-item .notification-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.08));
        border: 1px solid rgba(234, 88, 12, 0.1);
    }

    .notification-item .notification-icon i {
        color: #EA580C;
    }

    .notification-item .notification-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1f2937;
    }

    .notification-item .notification-message {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 2px;
        font-weight: 400;
    }

    .notification-item .notification-time {
        font-size: 0.6rem;
        color: #9ca3af;
        margin-top: 4px;
        font-weight: 400;
    }

    .notification-empty {
        padding: 24px;
        text-align: center;
        color: #9ca3af;
    }

    .notification-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 8px;
        color: #d5d0ca;
    }

    /* ========================================== */
    /* PROFILE DROPDOWN - Glass with Orange Border */
    /* ========================================== */
    .profile-wrapper {
        position: relative;
    }

    .profile-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 6px 12px 6px 6px;
        border-radius: 50px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 2px solid rgba(234, 88, 12, 0.15);
    }

    .profile-btn:hover {
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(249, 115, 22, 0.35);
    }

    .profile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F97316, #EA580C);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .profile-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: #1f2937;
    }

    .profile-chevron {
        color: #9ca3af;
        font-size: 0.7rem;
        transition: transform 0.3s ease;
    }

    .profile-chevron.rotated {
        transform: rotate(180deg);
    }

    .profile-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 10px);
        min-width: 240px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 2px solid rgba(234, 88, 12, 0.2);
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        z-index: 100;
    }

    .profile-dropdown.open {
        display: block;
    }

    .profile-dropdown .dropdown-header {
        padding: 16px 20px;
        border-bottom: 2px solid rgba(234, 88, 12, 0.12);
        background: rgba(255, 255, 255, 0.5);
    }

    .profile-dropdown .dropdown-header .user-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #F97316, #EA580C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .profile-dropdown .dropdown-header .user-email {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 400;
    }

    .profile-dropdown .dropdown-header .user-role {
        display: inline-block;
        margin-top: 4px;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 12px;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.1);
        color: #EA580C;
        border: 1px solid rgba(249, 115, 22, 0.15);
    }

    .profile-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        border-bottom: 1px solid rgba(234, 88, 12, 0.06);
        font-weight: 500;
    }

    .profile-dropdown .dropdown-item:hover {
        background: rgba(249, 115, 22, 0.06);
        color: #1f2937;
    }

    .profile-dropdown .dropdown-item i {
        width: 20px;
        color: #EA580C;
        font-size: 1.1rem;
    }

    .profile-dropdown .dropdown-item.librarian-link {
        background: rgba(249, 115, 22, 0.08);
        border-left: 3px solid #EA580C;
        color: #EA580C;
    }

    .profile-dropdown .dropdown-item.librarian-link:hover {
        background: rgba(249, 115, 22, 0.15);
        color: #C2410C;
    }

    .profile-dropdown .dropdown-divider {
        border-top: 2px solid rgba(234, 88, 12, 0.1);
        margin: 4px 0;
    }

    .profile-dropdown .dropdown-item.logout {
        color: #dc2626;
    }

    .profile-dropdown .dropdown-item.logout:hover {
        background: rgba(220, 38, 38, 0.04);
        color: #dc2626;
    }

    .profile-dropdown .dropdown-item.logout i {
        color: #dc2626;
    }

    @media (max-width: 768px) {
        .profile-name {
            display: none;
        }
        .profile-btn {
            padding: 6px;
        }
        .profile-dropdown {
            min-width: 200px;
            right: -10px;
        }
        .notifications-dropdown {
            width: 320px;
            right: -60px;
        }
    }

    /* ========================================== */
    /* SECTION TITLES - Colorful                 */
    /* ========================================== */
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Playfair Display', serif;
    }

    .section-title .highlight {
        background: linear-gradient(135deg, #F97316, #EA580C, #C2410C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-title i {
        color: #EA580C;
        font-size: 1.4rem;
    }

    /* ========================================== */
    /* BOOK SHELF CARDS - Glass with Orange Border */
    /* ========================================== */
    .book-shelf-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 2px solid rgba(234, 88, 12, 0.15);
        border-radius: 10px;
        padding: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        color: #1f2937;
        box-shadow: 0 1px 4px rgba(234, 88, 12, 0.04);
    }

    .book-shelf-card:hover {
        transform: translateY(-4px);
        border-color: rgba(249, 115, 22, 0.3);
        background: rgba(255, 255, 255, 0.95);
        text-decoration: none;
        color: #1f2937;
        box-shadow: 0 8px 30px rgba(249, 115, 22, 0.08);
    }

    .book-shelf-card .book-title {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1f2937;
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .book-shelf-card .book-author {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
    }

    .book-shelf-card .book-meta {
        font-size: 0.65rem;
        color: #9ca3af;
        font-weight: 400;
    }

    /* ========================================== */
    /* FEATURED BOOKS - Glass with Orange Border */
    /* ========================================== */
    .featured-shelf {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 2px solid rgba(234, 88, 12, 0.2);
        border-radius: 18px;
        padding: 20px 18px 0;
        box-shadow: 0 2px 12px rgba(234, 88, 12, 0.06);
    }

    .featured-books-row {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding-bottom: 18px;
        scrollbar-width: thin;
    }

    .featured-books-row::-webkit-scrollbar {
        height: 6px;
    }

    .featured-books-row::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #F97316, #EA580C);
        border-radius: 99px;
    }

    .featured-shelf-beam {
        height: 12px;
        background: linear-gradient(90deg, rgba(249, 115, 22, 0.2), rgba(194, 65, 12, 0.2));
        border-radius: 0 0 10px 10px;
        margin: 0 -18px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .featured-book {
        flex: 0 0 132px;
        text-decoration: none;
        color: #1f2937;
        display: block;
        transition: transform 0.25s ease;
    }

    .featured-book:hover {
        transform: translateY(-6px);
    }

    .featured-book-cover {
        width: 132px;
        height: 178px;
        border-radius: 6px 8px 8px 6px;
        overflow: hidden;
        position: relative;
        box-shadow: -3px 0 0 rgba(234, 88, 12, 0.1) inset, 0 4px 12px rgba(0, 0, 0, 0.06);
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(194, 65, 12, 0.05));
        border: 2px solid rgba(234, 88, 12, 0.08);
    }

    .featured-book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured-book-cover .placeholder-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .featured-book-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 999px;
        backdrop-filter: blur(2px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .featured-book-badge.free {
        background: rgba(16, 185, 129, 0.85);
        color: white;
    }

    .featured-book-badge.paid {
        background: rgba(251, 191, 36, 0.9);
        color: #1f2937;
    }

    .featured-book-rank {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        color: #EA580C;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(234, 88, 12, 0.15);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .featured-book-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 10px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .featured-book-author {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 2px;
        font-weight: 500;
    }

    .featured-book-meta {
        font-size: 0.68rem;
        color: #34d399;
        font-weight: 700;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .featured-book-meta.muted {
        color: #9ca3af;
    }

    .featured-empty {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 178px;
        color: #9ca3af;
        font-size: 0.85rem;
        background: repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.05) 0px, rgba(255, 255, 255, 0.05) 6px, transparent 6px, transparent 18px);
        border-radius: 8px;
        border: 1px dashed rgba(234, 88, 12, 0.15);
        width: 100%;
    }

    /* ========================================== */
    /* FOOTER - Glass with Orange Border         */
    /* ========================================== */
    .library-footer {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        border-top: 2px solid rgba(234, 88, 12, 0.2);
        padding: 24px;
        text-align: center;
        color: #9ca3af;
        font-size: 0.8rem;
        box-shadow: 0 -2px 12px rgba(234, 88, 12, 0.04);
        font-weight: 500;
    }

    .library-footer span {
        color: #EA580C;
        font-weight: 700;
    }

    /* ========================================== */
    /* RESPONSIVE                                */
    /* ========================================== */
    @media (max-width: 640px) {
        .shelf-row {
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }
        .bookshelf-rack {
            padding: 16px 12px 8px;
        }
        .shelf-bay {
            padding: 0 8px 10px;
        }
        .spine-book-cover {
            flex: 0 0 16px;
            width: 16px;
            height: 90px;
        }
        .spine-book-cover:hover {
            height: 120px;
        }
        .book-spines-container {
            height: 110px;
        }
        .featured-book {
            flex: 0 0 110px;
        }
        .featured-book-cover {
            width: 110px;
            height: 148px;
        }
    }

    /* ========================================== */
    /* GRADIENT TEXT - Dark Blue to Brown        */
    /* ========================================== */
    .gradient-text-dark {
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    /* ========================================== */
    /* GRADIENT TEXT - Orange                    */
    /* ========================================== */
    .gradient-text {
        background: linear-gradient(135deg, #F97316, #EA580C, #C2410C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    /* ========================================== */
    /* ICON GRADIENT - Blue                      */
    /* ========================================== */
    .icon-gradient {
        background: linear-gradient(135deg, #3B82F6, #60A5FA);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .icon-gradient:hover {
        background: linear-gradient(135deg, #60A5FA, #3B82F6);
        box-shadow: 0 4px 24px rgba(59, 130, 246, 0.4);
        transform: scale(1.05);
    }

    .icon-gradient-sm {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    /* ========================================== */
    /* GLASS CARD - With Orange Border           */
    /* ========================================== */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 2px solid rgba(234, 88, 12, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 12px 48px rgba(249, 115, 22, 0.12);
        transform: translateY(-2px);
        border-color: rgba(249, 115, 22, 0.3);
    }

    .glass-card .card-title {
        font-weight: 700;
        color: #1f2937;
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ========================================== */
    /* TEXT COLORS - Colorful                    */
    /* ========================================== */
    .text-orange {
        color: #EA580C !important;
        font-weight: 600;
    }

    .text-orange-gradient {
        background: linear-gradient(135deg, #F97316, #EA580C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .text-blue {
        color: #3B82F6 !important;
        font-weight: 600;
    }

    .text-green {
        color: #10B981 !important;
        font-weight: 600;
    }

    .text-purple {
        color: #8B5CF6 !important;
        font-weight: 600;
    }

    .text-pink {
        color: #EC4899 !important;
        font-weight: 600;
    }

    .text-gray-dark {
        color: #1f2937 !important;
        font-weight: 600;
    }

    .text-gray-medium {
        color: #6b7280 !important;
        font-weight: 500;
    }

    .text-gray-light {
        color: #9ca3af !important;
        font-weight: 400;
    }

    /* ========================================== */
    /* BOOK TITLE GRADIENT - Dark Blue to Brown  */
    /* ========================================== */
    .book-title-gradient {
        background: linear-gradient(135deg, #04065cc9, #362504);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }
</style>
</head>
<body>

<!-- ========================================== -->
<!-- LIBRARY BACKGROUND WRAPPER                  -->
<!-- ========================================== -->
<div class="library-bg">
    <div class="content-wrapper">
        
        <!-- TOP BAR -->
        <header class="library-topbar">
            <a href="{{ route('home') ?? '/' }}" class="logo">
                <img src="{{ asset('images/logo.jpeg') }}" alt="JLIBRARY" class="h-8 w-auto rounded-lg">
                <span>JLIBRARY</span>
            </a>
            
            <div class="nav-links">
                @auth
                    <span class="text-white font-medium">
                        <i class="ti ti-library"></i> Library
                    </span>
                    
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notification-bell" class="notification-bell" onclick="toggleNotifications()">
                            <i class="ti ti-bell text-xl"></i>
                            <span id="notification-badge" class="badge hidden">0</span>
                        </button>
                        
                        <div id="notifications-dropdown" class="notifications-dropdown">
                            <div class="dropdown-header">
                                <h4>🔔 Notifications</h4>
                                <a href="{{ route('notifications.index') }}">View All</a>
                            </div>
                            <div id="notifications-list" class="dropdown-body">
                                <div class="notification-empty">
                                    <i class="ti ti-loader-2 animate-spin"></i>
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Dropdown -->
                   <!-- Profile Dropdown -->
<div class="profile-wrapper">
    <button class="profile-btn" onclick="toggleProfileDropdown()">
        <div class="profile-avatar">
            {{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}
        </div>
        <span class="profile-name">{{ auth()->user()->full_name ?? 'User' }}</span>
        <i class="ti ti-chevron-down profile-chevron" id="profile-chevron"></i>
    </button>
    
    <div class="profile-dropdown" id="profile-dropdown">
        <div class="dropdown-header">
            <p class="user-name">{{ auth()->user()->full_name }}</p>
            <p class="user-email">{{ auth()->user()->email }}</p>
            <span class="user-role">
                @if(auth()->user()->hasRole('librarian'))
                    📚 Librarian
                @elseif(auth()->user()->hasRole('super_admin'))
                    👑 Super Admin
                @elseif(auth()->user()->hasRole('admin'))
                    🛡️ Admin
                @elseif(auth()->user()->hasRole('institution_admin'))
                    🏢 Institution Admin
                @else
                    👤 Member
                @endif
            </span>
        </div>
        
        {{-- ========================================== --}}
        {{-- ✅ BACK TO DASHBOARD (ONLY FOR NORMAL USERS) --}}
        {{-- ========================================== --}}
        @if(!auth()->user()->hasRole('super_admin') && 
            !auth()->user()->hasRole('admin') && 
            !auth()->user()->hasRole('institution_admin') && 
            !auth()->user()->hasRole('librarian'))
            
            <a href="{{ route('dashboard') }}" class="dropdown-item" style="border-left: 3px solid #a855f7; background: rgba(168,85,247,0.06);">
                <i class="ti ti-arrow-left" style="color: #a855f7;"></i> Back to Dashboard
                <span class="ml-auto text-[10px] bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full">Home</span>
            </a>
            
            <div class="dropdown-divider"></div>
        @endif
        
        {{-- ========================================== --}}
        {{-- ADMIN PANEL LINKS (Unchanged)              --}}
        {{-- ========================================== --}}
        @if(auth()->user()->hasRole('librarian'))
            <a href="{{ route('librarian.dashboard') }}" class="dropdown-item librarian-link">
                <i class="ti ti-library"></i> 📚 Librarian Panel
                <span class="ml-auto text-[10px] bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full">Admin</span>
            </a>
        @endif
        
        @if(auth()->user()->hasRole('super_admin'))
            <a href="{{ route('super-admin.dashboard') }}" class="dropdown-item">
                <i class="ti ti-crown"></i> Super Dashboard
                <span class="ml-auto text-[10px] bg-yellow-500/20 text-yellow-300 px-2 py-0.5 rounded-full">Super</span>
            </a>
        @endif
        
        @if(auth()->user()->hasRole('institution_admin'))
            <a href="{{ route('institution.dashboard') }}" class="dropdown-item">
                <i class="ti ti-building"></i> Institution Panel
                <span class="ml-auto text-[10px] bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded-full">Admin</span>
            </a>
        @endif
        
        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                <i class="ti ti-dashboard"></i> Admin Panel
                <span class="ml-auto text-[10px] bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full">Admin</span>
            </a>
        @endif
        
        <div class="dropdown-divider"></div>
        
        {{-- ========================================== --}}
        {{-- LOGOUT                                    --}}
        {{-- ========================================== --}}
        <form method="POST" action="{{ route('logout') }}" class="dropdown-item logout" style="padding: 0;">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-5 py-3 text-left" style="color: inherit;">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>
    </div>
</div>


                    
                @else
                    <a href="{{ route('login') }}"><i class="ti ti-login"></i> Login</a>
                    <a href="{{ route('register') }}" class="btn-library-nav">Get Started</a>
                @endauth
            </div>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="ti ti-menu-2"></i>
            </button>
        </header>
        
        <!-- PAGE CONTENT -->
        <div class="container mx-auto px-4 py-6 max-w-7xl">
            @yield('content')
        </div>
        
        <!-- FOOTER -->
        <footer class="library-footer">
            <p>&copy; {{ date('Y') }} JLIBRARY. All rights reserved.</p>
        </footer>
        
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT                                 -->
<!-- ========================================== -->
<script>
    function toggleMobileMenu() {
        const nav = document.querySelector('.library-topbar .nav-links');
        if (nav) {
            nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
        }
    }
    
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profile-dropdown');
        const chevron = document.getElementById('profile-chevron');
        dropdown.classList.toggle('open');
        if (chevron) {
            chevron.classList.toggle('rotated');
        }
    }
    
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profile-dropdown');
        const wrapper = document.querySelector('.profile-wrapper');
        if (wrapper && dropdown && !wrapper.contains(e.target)) {
            dropdown.classList.remove('open');
            const chevron = document.getElementById('profile-chevron');
            if (chevron) {
                chevron.classList.remove('rotated');
            }
        }
    });
    
    const bell = document.getElementById('notification-bell');
    const notifDropdown = document.getElementById('notifications-dropdown');
    const badge = document.getElementById('notification-badge');
    
    function toggleNotifications() {
        notifDropdown.classList.toggle('open');
        if (!notifDropdown.classList.contains('open')) {
            return;
        }
        loadNotifications();
    }
    
    function loadNotifications() {
        const list = document.getElementById('notifications-list');
        list.innerHTML = `
            <div class="notification-empty">
                <i class="ti ti-loader-2 animate-spin"></i>
                Loading...
            </div>
        `;
        
        fetch('{{ route("notifications.latest") }}')
            .then(response => response.json())
            .then(data => {
                if (data.notifications.length === 0) {
                    list.innerHTML = `
                        <div class="notification-empty">
                            <i class="ti ti-bell-off"></i>
                            No notifications
                        </div>
                    `;
                    return;
                }
                
                list.innerHTML = data.notifications.map(n => `
                    <a href="${n.link || '#'}" class="notification-item ${n.is_read ? '' : 'unread'}">
                        <div class="flex items-start gap-3">
                            <div class="notification-icon">
                                <i class="ti ${n.icon || 'ti-bell'} text-purple-400 text-sm"></i>
                            </div>
                            <div>
                                <div class="notification-title">${n.title}</div>
                                <div class="notification-message">${n.message}</div>
                                <div class="notification-time">${n.created_at}</div>
                            </div>
                        </div>
                    </a>
                `).join('');
                
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(() => {
                list.innerHTML = `
                    <div class="notification-empty">
                        <i class="ti ti-alert-circle"></i>
                        Error loading notifications
                    </div>
                `;
            });
    }
    
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
            }
        });
    
    document.addEventListener('click', function(e) {
        if (notifDropdown && !notifDropdown.contains(e.target) && !bell.contains(e.target)) {
            notifDropdown.classList.remove('open');
        }
    });
</script>

@stack('scripts')
</body>
</html>