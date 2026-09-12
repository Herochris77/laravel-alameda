<x-app-layout>
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-soft: rgba(13, 148, 136, 0.12);
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --info: #3b82f6;
            --info-dark: #2563eb;
            --purple: #7c3aed;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --border: #e2e8f0;
            --surface: #ffffff;
            --soft-bg: #f8fafc;
        }

        .estacionamiento-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .parking-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--primary) 0%, #14b8a6 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(13, 148, 136, 0.18);
            overflow: hidden;
            animation: fadeSlideDown 0.55s ease both;
        }

        .parking-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .parking-hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .parking-hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            backdrop-filter: blur(8px);
            animation: softFloat 3.2s ease-in-out infinite;
        }

        .parking-hero-icon i {
            color: white;
            font-size: 1.8rem;
            margin: 0 !important;
        }

        .parking-title {
            margin: 0 0 6px 0;
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            letter-spacing: -0.035em;
            line-height: 1.08;
        }

        .parking-subtitle {
            margin: 0;
            opacity: 0.92;
            font-size: 1rem;
            line-height: 1.45;
        }

        .parking-live-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            font-size: 0.86rem;
            font-weight: 800;
            backdrop-filter: blur(8px);
            white-space: nowrap;
        }

        .parking-live-pill i {
            margin: 0 !important;
        }

        .live-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 rgba(34, 197, 94, 0.55);
            animation: pulseDot 1.5s infinite;
        }

        .estacionamiento-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .parking-toolbar-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 18px 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            animation: fadeUp 0.5s ease both;
        }

        .parking-toolbar-text {
            color: var(--text-muted);
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }

        .parking-toolbar-text-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .parking-toolbar-text-icon i {
            margin: 0 !important;
            animation: rotateSoft 2.2s linear infinite;
        }

        .parking-last-update {
            font-size: 0.82rem;
            color: var(--text-muted);
            background: var(--soft-bg);
            border: 1px solid #eef2f7;
            padding: 9px 12px;
            border-radius: 999px;
            font-weight: 800;
            white-space: nowrap;
        }

        .parking-message {
            border-radius: 20px !important;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08) !important;
            animation: fadeUp 0.45s ease both;
        }

        .parking-message .button {
            border-radius: 12px !important;
            margin-top: 8px !important;
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 20px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--border);
            background: white;
            animation: scaleFade 0.45s ease both;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.08s;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 999px;
            right: -56px;
            bottom: -66px;
            opacity: 0.22;
            transition: transform 0.3s ease;
        }

        .stat-card:hover::after {
            transform: scale(1.12);
        }

        .stat-card.disponible {
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
        }

        .stat-card.disponible::after {
            background: var(--success);
        }

        .stat-card.ocupado {
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
        }

        .stat-card.ocupado::after {
            background: var(--danger);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            flex-shrink: 0;
            z-index: 1;
        }

        .stat-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .stat-card.disponible .stat-icon {
            background: rgba(16, 185, 129, 0.14);
            color: var(--success-dark);
        }

        .stat-card.ocupado .stat-icon {
            background: rgba(239, 68, 68, 0.14);
            color: var(--danger-dark);
        }

        .stat-info {
            z-index: 1;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 900;
            line-height: 1;
            color: var(--text-main);
        }

        .stat-info p {
            margin: 5px 0 0;
            font-size: 0.86rem;
            color: var(--text-muted);
            font-weight: 800;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 900;
            margin: 30px 0 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--text-main);
            animation: fadeUp 0.45s ease both;
        }

        .section-title-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .section-title .icon-bg {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .section-title:hover .icon-bg {
            transform: rotate(-4deg) scale(1.04);
        }

        .section-title .icon-bg i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .section-title.entrada .icon-bg {
            background: rgba(13, 148, 136, 0.12);
            color: var(--primary);
        }

        .section-title.central .icon-bg {
            background: rgba(124, 58, 237, 0.12);
            color: var(--purple);
        }

        .section-count {
            font-size: 0.78rem;
            font-weight: 800;
            padding: 7px 11px;
            border-radius: 999px;
            background: var(--soft-bg);
            color: var(--text-muted);
            white-space: nowrap;
        }

        .cajones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
            gap: 16px;
        }

        .cajon-card {
            background: white;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
            border: 1px solid var(--border);
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease,
                border-color 0.22s ease,
                background 0.22s ease;
            position: relative;
            overflow: hidden;
            min-height: 248px;
            display: flex;
            flex-direction: column;
            animation: cardEnter 0.45s ease both;
        }

        .cajon-card:nth-child(1) { animation-delay: 0.02s; }
        .cajon-card:nth-child(2) { animation-delay: 0.05s; }
        .cajon-card:nth-child(3) { animation-delay: 0.08s; }
        .cajon-card:nth-child(4) { animation-delay: 0.11s; }
        .cajon-card:nth-child(5) { animation-delay: 0.14s; }
        .cajon-card:nth-child(6) { animation-delay: 0.17s; }

        .cajon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
        }

        .cajon-card::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            right: -60px;
            bottom: -70px;
            opacity: 0.12;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .cajon-card:hover::after {
            transform: scale(1.25);
            opacity: 0.18;
        }

        .cajon-card.disponible {
            border-color: rgba(16, 185, 129, 0.28);
        }

        .cajon-card.disponible::before {
            background: linear-gradient(90deg, var(--success), #34d399);
        }

        .cajon-card.disponible::after {
            background: var(--success);
        }

        .cajon-card.ocupado {
            border-color: rgba(239, 68, 68, 0.25);
        }

        .cajon-card.ocupado::before {
            background: linear-gradient(90deg, var(--danger), #f87171);
        }

        .cajon-card.ocupado::after {
            background: var(--danger);
        }

        .cajon-card.mio {
            border-color: rgba(59, 130, 246, 0.34);
            background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        }

        .cajon-card.mio::before {
            background: linear-gradient(90deg, var(--info), #60a5fa);
        }

        .cajon-card.mio::after {
            background: var(--info);
        }

        .cajon-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.13);
        }

        .cajon-card.updating {
            animation: pulseCard 0.6s ease;
        }

        .cajon-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .cajon-icon {
            width: 54px;
            height: 54px;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .cajon-card:hover .cajon-icon {
            transform: scale(1.06) rotate(-3deg);
        }

        .cajon-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .cajon-card.disponible .cajon-icon {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success-dark);
        }

        .cajon-card.ocupado .cajon-icon {
            background: rgba(239, 68, 68, 0.12);
            color: var(--danger-dark);
        }

        .cajon-card.mio .cajon-icon {
            background: rgba(59, 130, 246, 0.13);
            color: var(--info-dark);
        }

        .cajon-estado {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .cajon-estado i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .cajon-estado.disponible {
            background: rgba(16, 185, 129, 0.13);
            color: #047857;
        }

        .cajon-estado.ocupado {
            background: rgba(239, 68, 68, 0.13);
            color: #b91c1c;
        }

        .cajon-estado.mio {
            background: rgba(59, 130, 246, 0.14);
            color: #1d4ed8;
        }

        .cajon-body {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .cajon-nombre {
            font-size: 1.12rem;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .cajon-ubicacion {
            font-size: 0.86rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            font-weight: 700;
        }

        .cajon-ubicacion i {
            margin: 0 !important;
            color: var(--text-soft);
        }

        .cajon-usuario {
            background: var(--soft-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 14px;
        }

        .cajon-card.mio .cajon-usuario {
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.16);
        }

        .cajon-usuario-label {
            font-size: 0.68rem;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 5px;
            font-weight: 900;
        }

        .cajon-usuario-nombre {
            font-weight: 800;
            color: var(--text-main);
            font-size: 0.94rem;
            word-break: break-word;
        }

        .cajon-actions {
            margin-top: auto;
            position: relative;
            z-index: 1;
        }

        .btn-accion {
            width: 100%;
            min-height: 46px;
            padding: 13px 14px;
            border: none;
            border-radius: 14px;
            font-weight: 900;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            letter-spacing: 0.02em;
        }

        .btn-accion i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .btn-ocupar {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: white;
        }

        .btn-ocupar:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(239, 68, 68, 0.32);
        }

        .btn-liberar {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
        }

        .btn-liberar:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.32);
        }

        .btn-accion:active {
            transform: scale(0.98);
        }

        .btn-accion:disabled {
            opacity: 0.72;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .loading-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
            gap: 16px;
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 22px;
            height: 248px;
        }

        .empty-state {
            text-align: center;
            padding: 64px 20px;
            color: var(--text-soft);
            background: white;
            border-radius: 22px;
            border: 1px dashed #cbd5e1;
            animation: fadeUp 0.45s ease both;
        }

        .empty-state i {
            font-size: 3.4rem;
            margin: 0 0 16px !important;
            opacity: 0.6;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            color: #475569;
            font-size: 1.15rem;
            font-weight: 900;
        }

        .empty-state p {
            margin: 0;
            color: var(--text-soft);
        }

        @keyframes fadeSlideDown {
            from {
                opacity: 0;
                transform: translateY(-14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleFade {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes cardEnter {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        @keyframes pulseDot {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.55);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        @keyframes pulseCard {
            0% {
                transform: scale(1);
            }
            45% {
                transform: scale(1.015);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes softFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-4px);
            }
        }

        @keyframes rotateSoft {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .estacionamiento-page {
                padding-bottom: 18px;
            }

            .parking-hero {
                border-radius: 0 0 24px 24px;
                margin: -8px -4px 18px;
                padding: 22px;
            }

            .parking-hero-content {
                align-items: stretch;
            }

            .parking-hero-left {
                align-items: flex-start;
            }

            .parking-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
            }

            .parking-title {
                font-size: 1.55rem;
            }

            .parking-subtitle {
                font-size: 0.92rem;
            }

            .parking-live-pill {
                width: 100%;
                justify-content: center;
                border-radius: 16px;
            }

            .estacionamiento-container {
                padding: 0;
            }

            .parking-toolbar-card {
                align-items: flex-start;
                margin-bottom: 16px;
                border-radius: 18px;
                padding: 16px;
            }

            .parking-toolbar-text {
                font-size: 0.86rem;
            }

            .parking-last-update {
                width: 100%;
                text-align: center;
                border-radius: 14px;
            }

            .stats-bar {
                gap: 10px;
                margin-bottom: 18px;
            }

            .stat-card {
                padding: 14px;
                border-radius: 18px;
                gap: 10px;
            }

            .stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                font-size: 1.2rem;
            }

            .stat-info h3 {
                font-size: 1.55rem;
            }

            .stat-info p {
                font-size: 0.76rem;
            }

            .section-title {
                margin: 24px 0 12px;
                font-size: 1rem;
            }

            .section-title .icon-bg {
                width: 34px;
                height: 34px;
                border-radius: 11px;
            }

            .cajones-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .cajon-card {
                min-height: 224px;
                padding: 14px;
                border-radius: 18px;
            }

            .cajon-top {
                flex-direction: column;
                gap: 10px;
                margin-bottom: 12px;
            }

            .cajon-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
                font-size: 1.3rem;
            }

            .cajon-estado {
                font-size: 0.7rem;
                padding: 6px 9px;
            }

            .cajon-nombre {
                font-size: 1rem;
            }

            .cajon-ubicacion {
                font-size: 0.78rem;
                margin-bottom: 12px;
            }

            .cajon-usuario {
                padding: 10px;
                margin-bottom: 12px;
            }

            .cajon-usuario-label {
                font-size: 0.62rem;
            }

            .cajon-usuario-nombre {
                font-size: 0.84rem;
            }

            .btn-accion {
                min-height: 44px;
                font-size: 0.78rem;
                padding: 11px 10px;
            }

            .loading-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .loading-shimmer {
                height: 224px;
                border-radius: 18px;
            }
        }

        @media (max-width: 420px) {
            .stats-bar {
                grid-template-columns: 1fr 1fr;
            }

            .stat-card {
                padding: 12px;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
                font-size: 1.05rem;
            }

            .stat-info h3 {
                font-size: 1.35rem;
            }

            .stat-info p {
                font-size: 0.7rem;
            }

            .cajones-grid {
                grid-template-columns: 1fr;
            }

            .loading-grid {
                grid-template-columns: 1fr;
            }

            .cajon-card {
                min-height: auto;
            }

            .cajon-top {
                flex-direction: row;
                align-items: center;
            }

            .btn-accion {
                font-size: 0.86rem;
            }
        }

        @media (hover: none) {
            .cajon-card:hover,
            .btn-ocupar:hover,
            .btn-liberar:hover {
                transform: none;
            }

            .cajon-card:hover .cajon-icon {
                transform: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <div class="estacionamiento-page">
        <div class="parking-hero">
            <div class="parking-hero-content">
                <div class="parking-hero-left">
                    <div class="parking-hero-icon">
                        <i class="car icon"></i>
                    </div>

                    <div>
                        <h1 class="parking-title">Estacionamiento y áreas comunes</h1>
                        <p class="parking-subtitle">Cajones de estacionamiento y escalera comunitaria en tiempo real</p>
                    </div>
                </div>

                <div class="parking-live-pill">
                    <span class="live-dot"></span>
                    Actualización automática cada 10 segundos
                </div>
            </div>
        </div>

        <div class="estacionamiento-container">
            <div class="parking-toolbar-card">
                <div class="parking-toolbar-text">
                    <div class="parking-toolbar-text-icon">
                        <i class="sync alternate icon"></i>
                    </div>

                    <span>
                        La disponibilidad se mantiene sincronizada automáticamente. Puedes ocupar o liberar tu cajón desde esta pantalla.
                    </span>
                </div>

                <div class="parking-last-update" id="ultima-actualizacion"></div>
            </div>

            <div class="ui warning message parking-message" id="alerta-inicializar" style="display: none; margin-bottom: 24px;">
                <div class="header">
                    <i class="info circle icon"></i>
                    Sin cajones configurados
                </div>

                <p>No hay cajones de estacionamiento. ¿Deseas crearlos?</p>

                <button class="ui primary button" onclick="inicializarCajones()">
                    <i class="plus icon"></i>
                    Crear cajones
                </button>
            </div>

            <div id="stats-bar" class="stats-bar"></div>
            <div id="cajones-container"></div>
        </div>
    </div>

    <script>
        let datosCajones = [];
        let primeraCarga = true;
        let actualizandoSilencioso = false;
        let esSuperAdmin = false;

        $(document).ready(function() {
            cargarEstacionamientos();

            setInterval(function() {
                actualizarDisponibilidad(true);
            }, 10000);
        });

        function actualizarHoraConsulta() {
            const ahora = new Date();
            const hora = ahora.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

            $('#ultima-actualizacion').text(`Última actualización: ${hora}`);
        }

        function mostrarSkeleton() {
            $('#cajones-container').html(`
                <div class="loading-grid">
                    <div class="loading-shimmer"></div>
                    <div class="loading-shimmer"></div>
                    <div class="loading-shimmer"></div>
                    <div class="loading-shimmer"></div>
                </div>
            `);
        }

        function cargarEstacionamientos() {
            mostrarSkeleton();

            $.get('{{ route("usuario.estacionamiento.disponibilidad") }}', function(response) {
                if (response.success) {
                    esSuperAdmin = response.es_super_admin === true;
                    datosCajones = response.estacionamientos || [];

                    if (datosCajones.length === 0) {
                        $('#alerta-inicializar').show();
                        $('#stats-bar').html('');
                        $('#cajones-container').html('');
                    } else {
                        $('#alerta-inicializar').hide();
                        renderizarStats();
                        renderizarCajones(datosCajones);
                    }

                    actualizarHoraConsulta();
                    primeraCarga = false;
                }
            }).fail(function() {
                $('#cajones-container').html(`
                    <div class="empty-state">
                        <i class="warning sign icon"></i>
                        <h3>Error al cargar disponibilidad</h3>
                        <p>Intenta actualizar la página o revisa tu conexión.</p>
                    </div>
                `);
            });
        }

        function renderizarStats() {
            // Las estadísticas cuentan solo cajones de estacionamiento (no la escalera).
            const cajones = datosCajones.filter(c => c.ubicacion !== 'escalera');
            const disponibles = cajones.filter(c => !c.esta_ocupado).length;
            const ocupados = cajones.filter(c => c.esta_ocupado).length;

            $('#stats-bar').html(`
                <div class="stat-card disponible">
                    <div class="stat-icon">
                        <i class="check circle icon"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${disponibles}</h3>
                        <p>Disponibles</p>
                    </div>
                </div>

                <div class="stat-card ocupado">
                    <div class="stat-icon">
                        <i class="times circle icon"></i>
                    </div>
                    <div class="stat-info">
                        <h3>${ocupados}</h3>
                        <p>Ocupados</p>
                    </div>
                </div>
            `);
        }

        window.inicializarCajones = function() {
            mostrarLoaderPantalla();

            $.ajax({
                url: '{{ route("usuario.estacionamiento.inicializar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        cargarEstacionamientos();
                    } else {
                        alertify.error(response.message || 'No se pudieron crear los cajones.');
                    }
                },
                error: function() {
                    alertify.error('Error al crear cajones');
                },
                complete: function() {
                    ocultarLoaderPantalla();
                }
            });
        };

        function renderizarCajones(estacionamientos) {
            let html = '';

            const entrada = estacionamientos.filter(e => e.ubicacion === 'entrada');
            const central = estacionamientos.filter(e => e.ubicacion === 'central');
            const escalera = estacionamientos.filter(e => e.ubicacion === 'escalera');

            if (entrada.length > 0) {
                html += `
                    <h4 class="section-title entrada">
                        <span class="section-title-left">
                            <span class="icon-bg">
                                <i class="sign in icon"></i>
                            </span>
                            Cajones de Entrada
                        </span>

                        <span class="section-count">${entrada.length} cajones</span>
                    </h4>

                    <div class="cajones-grid">
                `;

                entrada.forEach(cajon => html += crearCajonCard(cajon));

                html += `</div>`;
            }

            if (central.length > 0) {
                html += `
                    <h4 class="section-title central">
                        <span class="section-title-left">
                            <span class="icon-bg">
                                <i class="map marker alternate icon"></i>
                            </span>
                            Cajones Centrales
                        </span>

                        <span class="section-count">${central.length} cajones</span>
                    </h4>

                    <div class="cajones-grid">
                `;

                central.forEach(cajon => html += crearCajonCard(cajon));

                html += `</div>`;
            }

            if (escalera.length > 0) {
                html += `
                    <h4 class="section-title escalera">
                        <span class="section-title-left">
                            <span class="icon-bg">
                                <i class="toolbox icon"></i>
                            </span>
                            Escalera comunitaria
                        </span>

                        <span class="section-count">${escalera.length} recurso</span>
                    </h4>

                    <div class="cajones-grid">
                `;

                escalera.forEach(cajon => html += crearCajonCard(cajon));

                html += `</div>`;
            }

            if (html === '') {
                html = `
                    <div class="empty-state">
                        <i class="car icon"></i>
                        <h3>No hay cajones disponibles</h3>
                        <p>Intenta actualizar la página o crea los cajones iniciales.</p>
                    </div>
                `;
            }

            $('#cajones-container').html(html);
        }

        function crearCajonCard(cajon) {
            const esEscalera = cajon.ubicacion === 'escalera';

            let cardClass = 'disponible';
            let badgeClass = 'disponible';
            let badgeText = '<i class="check icon"></i> Disponible';
            let btnHtml = '';
            let usuarioHtml = '';
            let iconClass = esEscalera ? 'toolbox' : 'car';

            if (cajon.es_mio) {
                cardClass = 'mio';
                badgeClass = 'mio';
                badgeText = esEscalera
                    ? '<i class="star icon"></i> Tú la ocupas'
                    : '<i class="star icon"></i> Tú ocupas';
                iconClass = 'user';

                btnHtml = `
                    <button class="btn-accion btn-liberar" onclick="liberarCajon(${cajon.id}, '${escapeJs(cajon.nombre)}')">
                        <i class="unlock icon"></i>
                        Liberar
                    </button>
                `;

                usuarioHtml = `
                    <div class="cajon-usuario">
                        <div class="cajon-usuario-label">Ocupado por</div>
                        <div class="cajon-usuario-nombre">Tú</div>
                    </div>
                `;
            } else if (cajon.esta_ocupado) {
                cardClass = 'ocupado';
                badgeClass = 'ocupado';
                badgeText = '<i class="times icon"></i> Ocupado';
                iconClass = 'user';

                if (cajon.usuario) {
                    usuarioHtml = `
                        <div class="cajon-usuario">
                            <div class="cajon-usuario-label">Ocupado por</div>
                            <div class="cajon-usuario-nombre">
                                ${escapeHtml(cajon.usuario.nombre || 'Usuario')} #${escapeHtml(cajon.usuario.casa || 'N/A')}
                            </div>
                        </div>
                    `;
                }

                // El super-administrador puede desocupar cualquier lugar.
                if (esSuperAdmin) {
                    const ocupanteTxt = cajon.usuario
                        ? `${cajon.usuario.nombre || 'Usuario'} #${cajon.usuario.casa || 'N/A'}`
                        : '';
                    btnHtml = `
                        <button class="btn-accion btn-liberar" onclick="liberarCajonAdmin(${cajon.id}, '${escapeJs(cajon.nombre)}', '${escapeJs(ocupanteTxt)}')">
                            <i class="unlock icon"></i>
                            Desocupar (admin)
                        </button>
                    `;
                }
            } else {
                btnHtml = `
                    <button class="btn-accion btn-ocupar" onclick="ocuparCajon(${cajon.id}, '${escapeJs(cajon.nombre)}', this)">
                        <i class="lock icon"></i>
                        Ocupar
                    </button>
                `;
            }

            const ubicacionTexto = cajon.ubicacion === 'entrada'
                ? 'Entrada'
                : (cajon.ubicacion === 'escalera' ? 'Recurso compartido' : 'Zona Central');

            return `
                <div class="cajon-card ${cardClass}" data-id="${cajon.id}">
                    <div class="cajon-top">
                        <div class="cajon-icon">
                            <i class="${iconClass} icon"></i>
                        </div>

                        <div class="cajon-estado ${badgeClass}">
                            ${badgeText}
                        </div>
                    </div>

                    <div class="cajon-body">
                        <div class="cajon-nombre">${escapeHtml(cajon.nombre)}</div>

                        <div class="cajon-ubicacion">
                            <i class="map marker alternate icon"></i>
                            ${ubicacionTexto}
                        </div>

                        ${usuarioHtml}
                    </div>

                    <div class="cajon-actions">
                        ${btnHtml}
                    </div>
                </div>
            `;
        }

        window.ocuparCajon = function(id, nombre, buttonElement) {
            const btn = buttonElement ? $(buttonElement) : $(event.target).closest('button');

            btn.prop('disabled', true).addClass('loading');
            btn.closest('.cajon-card').addClass('updating');

            $.ajax({
                url: '{{ route("usuario.estacionamiento.ocupar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    estacionamiento_id: id
                },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        cargarEstacionamientos();
                    } else {
                        alertify.error(response.message || 'No se pudo ocupar el cajón.');
                        btn.prop('disabled', false).removeClass('loading');
                        btn.closest('.cajon-card').removeClass('updating');
                    }
                },
                error: function(xhr) {
                    alertify.error(xhr.responseJSON?.message || 'Error al ocupar');
                    btn.prop('disabled', false).removeClass('loading');
                    btn.closest('.cajon-card').removeClass('updating');
                }
            });
        };

        window.liberarCajon = function(id, nombre) {
            alertify.confirm(
                'Liberar cajón',
                `¿Liberar "${nombre}"? Quedará disponible para otros.`,
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(() => {
                        $.ajax({
                            url: '{{ route("usuario.estacionamiento.liberar") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                estacionamiento_id: id
                            },
                            success: function(response) {
                                if (response.success) {
                                    alertify.success(response.message);
                                    cargarEstacionamientos();
                                } else {
                                    alertify.error(response.message || 'No se pudo liberar el cajón.');
                                }
                            },
                            error: function() {
                                alertify.error('Error al liberar');
                            },
                            complete: function() {
                                ocultarLoaderPantalla();
                            }
                        });
                    }, 100);
                },
                function() {}
            ).set('labels', {
                ok: 'Sí, liberar',
                cancel: 'Cancelar'
            });
        };

        // Solo super-administrador: desocupar un lugar ocupado por otro usuario.
        window.liberarCajonAdmin = function(id, nombre, ocupante) {
            alertify.confirm(
                'Desocupar lugar',
                `Vas a desocupar "${nombre}"${ocupante ? ` (lo ocupa ${ocupante})` : ''}. ¿Continuar?`,
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(() => {
                        $.ajax({
                            url: '{{ route("usuario.estacionamiento.liberar") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                estacionamiento_id: id
                            },
                            success: function(response) {
                                if (response.success) {
                                    alertify.success(response.message);
                                    cargarEstacionamientos();
                                } else {
                                    alertify.error(response.message || 'No se pudo desocupar el lugar.');
                                }
                            },
                            error: function() {
                                alertify.error('Error al desocupar');
                            },
                            complete: function() {
                                ocultarLoaderPantalla();
                            }
                        });
                    }, 100);
                },
                function() {}
            ).set('labels', {
                ok: 'Sí, desocupar',
                cancel: 'Cancelar'
            });
        };

        function actualizarDisponibilidad(silencioso = false) {
            actualizandoSilencioso = silencioso;

            $.get('{{ route("usuario.estacionamiento.disponibilidad") }}', function(response) {
                if (response.success) {
                    esSuperAdmin = response.es_super_admin === true;
                    datosCajones = response.estacionamientos || [];
                    renderizarStats();
                    renderizarCajones(datosCajones);
                    actualizarHoraConsulta();
                }
            });
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeJs(text) {
            return String(text || '')
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/\n/g, '\\n')
                .replace(/\r/g, '\\r');
        }
    </script>
</x-app-layout>