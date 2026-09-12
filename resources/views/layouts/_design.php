<style>
    /* ========================================
       DESIGN SYSTEM - Alameda
       Variables, componentes y utilerías
       ======================================== */

    /* ---- Variables ---- */
    :root {
        /* Primary */
        --primary-start: #667eea;
        --primary-end: #764ba2;
        --primary-50: #f0f4ff;
        --primary-100: #e0e7ff;

        /* Text */
        --text-primary: #1e293b;
        --text-secondary: #475569;
        --text-muted: #64748b;
        --text-light: #94a3b8;

        /* Backgrounds */
        --bg-body: #f5f7fa;
        --bg-white: #ffffff;
        --bg-gray: #f1f5f9;
        --bg-gray-hover: #e2e8f0;

        /* Borders */
        --border: #e2e8f0;
        --border-light: #f1f5f9;
        --border-input: #e5e7eb;

        /* Status */
        --success: #10b981;
        --success-dark: #059669;
        --success-bg: #d1fae5;
        --success-text: #065f46;

        --danger: #ef4444;
        --danger-dark: #dc2626;
        --danger-bg: #fee2e2;
        --danger-text: #991b1b;

        --warning: #f59e0b;
        --warning-bg: #fef3c7;
        --warning-text: #92400e;

        --info: #3b82f6;
        --info-bg: #dbeafe;
        --info-text: #1e40af;

        /* Spacing */
        --sp-xs: 4px;
        --sp-sm: 8px;
        --sp-md: 16px;
        --sp-lg: 20px;
        --sp-xl: 24px;
        --sp-2xl: 32px;

        /* Border Radius */
        --radius-sm: 8px;
        --radius-md: 10px;
        --radius-lg: 12px;
        --radius-xl: 16px;

        /* Shadows */
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 2px 12px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.1);

        /* Font */
        --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

        /* Layout */
        --navbar-height: 64px;
        --page-max-width: 1400px;

        /* Transition */
        --transition: 0.2s ease;
    }

    /* ---- Base ---- */
    * {
        font-family: var(--font-family);
    }

    body {
        background: var(--bg-body);
        min-height: 100vh;
        overflow-x: hidden;
    }

    html {
        overflow-x: hidden;
    }

    .page-container,
    main {
        overflow-x: hidden;
        max-width: 100vw;
    }

    /* ---- Page Layout ---- */
    .page-container {
        max-width: var(--page-max-width);
        margin: 0 auto;
        padding: var(--sp-xl) var(--sp-lg);
    }

    @media (max-width: 768px) {
        .page-container {
            padding: var(--sp-md) var(--sp-sm);
        }
    }

    /* ---- Page Header ---- */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: var(--sp-xl);
        flex-wrap: wrap;
        gap: var(--sp-md);
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: var(--sp-md);
    }

    .page-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--primary-start) 0%, var(--primary-end) 100%);
        border-radius: var(--radius-xl);
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .page-icon i,
    .menu-card-icon i,
    .card-body>i[class*="icon"],
    .stat-card i,
    .menu-card i,
    .opcion-item>i,
    .resultado-info>i,
    [class*="icon-container"] i,
    .icon-centered i,
    div[style*="align-items: center"][style*="justify-content: center"] i {
        margin: 0 !important;
        line-height: 1 !important;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .page-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin: var(--sp-xs) 0 0 0;
    }

    .page-header-actions {
        display: flex;
        gap: var(--sp-sm);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header-actions {
            width: 100%;
        }

        .page-header-actions .btn {
            flex: 1;
        }
    }

    /* ---- Cards ---- */
    .card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .card-header {
        padding: var(--sp-lg) var(--sp-xl);
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-body {
        padding: var(--sp-xl);
    }

    .card-header i,
    .card-header .icon {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
        line-height: 1 !important;
    }

    /* ---- Stat Cards ---- */
    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: var(--sp-lg);
        box-shadow: var(--shadow-md);
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: var(--sp-sm);
    }

    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    /* ---- Buttons ---- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--sp-sm);
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all var(--transition);
        text-decoration: none;
        line-height: 1.4;
    }

    .btn:active {
        transform: scale(0.98);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-start) 0%, var(--primary-end) 100%);
        color: white;
    }

    .btn-primary:hover {
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--bg-gray);
        color: var(--text-secondary);
    }

    .btn-secondary:hover {
        background: var(--bg-gray-hover);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: var(--success-dark);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: var(--danger-dark);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn-sm {
        padding: 8px 14px;
        font-size: 0.85rem;
    }

    .btn-lg {
        padding: 14px 28px;
        font-size: 1rem;
    }

    /* ---- Form Elements ---- */
    .form-group {
        margin-bottom: var(--sp-lg);
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: var(--sp-sm);
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border-input);
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        transition: all var(--transition);
        background: var(--bg-white);
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-start);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input.error {
        border-color: var(--danger);
    }

    /* ---- Upload Area ---- */
    .upload-area {
        border: 2px dashed var(--border);
        border-radius: var(--radius-xl);
        padding: var(--sp-sm);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8fafc;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .upload-area:hover,
    .upload-area.dragover {
        border-color: var(--primary-start);
        background: var(--primary-50);
    }

    .upload-area label {
        cursor: pointer;
        width: 100%;
    }

    .upload-placeholder {
        padding: 30px 20px;
    }

    .upload-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-start) 0%, var(--primary-end) 100%);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--sp-md);
        transition: transform 0.3s ease;
    }

    .upload-area:hover .upload-icon {
        transform: scale(1.1) translateY(-5px);
    }

    .upload-icon i {
        font-size: 24px;
        color: white;
        margin: 0 !important;
    }

    .upload-text .primary {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: var(--sp-xs);
    }

    .upload-text .secondary {
        display: block;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .upload-formats {
        font-size: 0.75rem;
        color: var(--text-light);
        margin-top: var(--sp-sm);
    }

    /* ---- Tables ---- */
    .dataTables_wrapper {
        padding: 0 !important;
        max-width: 100%;
    }

    .dataTables_wrapper table {
        width: 100% !important;
    }

    table.dataTable {
        max-width: none !important;
    }

    .dataTables_scroll {
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_paginate {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            text-align: left;
            -webkit-overflow-scrolling: touch;
        }

        .dataTables_wrapper .dataTables_paginate .ui.pagination.menu {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: max-content !important;
            min-width: 0 !important;
        }

        .dataTables_wrapper .dataTables_paginate .ui.pagination.menu .item {
            flex: 0 0 auto !important;
            width: auto !important;
            white-space: nowrap;
        }
    }

    /* ---- Empty State ---- */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: var(--bg-gray);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--sp-lg);
        font-size: 2rem;
        color: var(--text-light);
    }

    .empty-state h3 {
        color: var(--text-secondary);
        margin-bottom: var(--sp-sm);
    }

    .empty-state p {
        color: var(--text-light);
    }

    /* ---- Alerts ---- */
    .alert {
        padding: 14px 16px;
        border-radius: var(--radius-lg);
        margin-bottom: var(--sp-lg);
        display: flex;
        align-items: center;
        gap: var(--sp-sm);
    }

    .alert-success {
        background: var(--success-bg);
        color: var(--success-text);
    }

    .alert-error {
        background: var(--danger-bg);
        color: var(--danger-text);
    }

    .alert-warning {
        background: var(--warning-bg);
        color: var(--warning-text);
    }

    .alert-info {
        background: var(--info-bg);
        color: var(--info-text);
    }

    /* ---- Grid ---- */
    .grid {
        display: grid;
        gap: var(--sp-lg);
    }

    .grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .grid-4 {
        grid-template-columns: repeat(4, 1fr);
    }

    .grid-sidebar {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: var(--sp-xl);
        align-items: start;
    }

    .grid-sidebar-sm {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: var(--sp-xl);
        align-items: start;
    }

    @media (max-width: 1024px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 900px) {
        .grid-sidebar,
        .grid-sidebar-sm {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .grid-4,
        .grid-3,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* ---- Loader ---- */
    .loader-overlay {
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
    }

    .loader-overlay.active {
        display: flex;
    }

    /* ---- Icon Helpers ---- */
    .circular-icon,
    [style*="border-radius: 50%"] i {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .circular-icon i,
    [style*="border-radius: 50%"] i {
        margin: 0 !important;
        line-height: 1 !important;
    }
</style>
