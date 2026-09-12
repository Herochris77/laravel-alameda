<x-app-layout>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-soft: rgba(59, 130, 246, 0.12);
            --success: #10b981;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --border: #e2e8f0;
            --surface: #ffffff;
            --soft-bg: #f8fafc;
        }

        .vehiculos-page {
            width: 100%;
            max-width: 100%;
            padding-bottom: 24px;
        }

        .vehiculos-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
            overflow: hidden;
            animation: fadeSlideDown 0.55s ease both;
        }

        .vehiculos-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .vehiculos-hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .vehiculos-hero-icon {
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

        .vehiculos-hero-icon i {
            color: white;
            font-size: 1.8rem;
            margin: 0 !important;
        }

        .vehiculos-title {
            margin: 0 0 6px 0;
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            letter-spacing: -0.035em;
            line-height: 1.08;
        }

        .vehiculos-subtitle {
            margin: 0;
            opacity: 0.92;
            font-size: 1rem;
            line-height: 1.45;
        }

        .vehiculos-hero-action .btn {
            min-height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        }

        .vehiculos-toolbar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 220px auto;
            gap: 14px;
            align-items: end;
            animation: fadeUp 0.5s ease both;
        }

        .filter-group {
            min-width: 0;
        }

        .filter-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .filter-control {
            position: relative;
        }

        .filter-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-soft);
            pointer-events: none;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            min-height: 46px;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 10px 14px 10px 42px;
            font-size: 0.95rem;
            color: var(--text-main);
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            box-sizing: border-box;
        }

        .filter-select {
            appearance: auto;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: rgba(59, 130, 246, 0.55);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
            transform: translateY(-1px);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-filter-clear {
            min-height: 46px;
            border-radius: 14px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .vehiculos-count {
            margin: -4px 0 16px 2px;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 700;
            animation: fadeUp 0.4s ease both;
        }

        .vehiculos-list {
            display: grid;
            gap: 16px;
        }

        .vehiculo-card {
            background: white;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease,
                border-color 0.22s ease,
                background 0.22s ease;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            animation: cardEnter 0.45s ease both;
        }

        .vehiculo-card:nth-child(1) { animation-delay: 0.02s; }
        .vehiculo-card:nth-child(2) { animation-delay: 0.05s; }
        .vehiculo-card:nth-child(3) { animation-delay: 0.08s; }
        .vehiculo-card:nth-child(4) { animation-delay: 0.11s; }
        .vehiculo-card:nth-child(5) { animation-delay: 0.14s; }

        .vehiculo-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
        }

        .vehiculo-card::after {
            content: '';
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 999px;
            right: -70px;
            bottom: -80px;
            background: var(--primary);
            opacity: 0.08;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .vehiculo-card:hover {
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
            transform: translateY(-4px);
            border-color: rgba(59, 130, 246, 0.35);
        }

        .vehiculo-card:hover::after {
            transform: scale(1.25);
            opacity: 0.13;
        }

        .vehiculo-card.is-hidden {
            display: none;
        }

        .vehiculo-foto {
            width: 140px;
            height: 94px;
            border-radius: 16px;
            object-fit: cover;
            background: var(--soft-bg);
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .vehiculo-foto img {
            width: 100%;
            height: 100%;
            border-radius: 16px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .vehiculo-card:hover .vehiculo-foto img {
            transform: scale(1.05);
        }

        .vehiculo-foto i {
            font-size: 2.15rem;
            color: var(--text-soft);
            margin: 0 !important;
        }

        .vehiculo-info {
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .vehiculo-main-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 7px;
        }

        .vehiculo-placas {
            font-size: 1.18rem;
            font-weight: 900;
            color: var(--text-main);
            letter-spacing: 0.03em;
            word-break: break-word;
        }

        .vehiculo-marca {
            font-size: 0.96rem;
            color: #475569;
            margin-bottom: 10px;
            line-height: 1.4;
            word-break: break-word;
            font-weight: 700;
        }

        .vehiculo-detalles {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
            font-size: 0.84rem;
            color: var(--text-muted);
        }

        .vehiculo-detalles span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            background: var(--soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 700;
        }

        .vehiculo-detalles i {
            margin: 0 !important;
            line-height: 1 !important;
            flex-shrink: 0;
            color: var(--primary);
        }

        .vehiculo-tipo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .vehiculo-tipo i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .vehiculo-tipo.automovil {
            background: #dbeafe;
            color: #2563eb;
        }

        .vehiculo-tipo.motocicleta {
            background: #fef3c7;
            color: #d97706;
        }

        .vehiculo-tipo.camioneta {
            background: #d1fae5;
            color: #059669;
        }

        .vehiculo-tipo.otro {
            background: #f1f5f9;
            color: #64748b;
        }

        .vehiculo-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
            align-items: center;
            justify-content: flex-end;
            position: relative;
            z-index: 1;
        }

        .btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .btn-icon:hover {
            transform: translateY(-2px) scale(1.04);
        }

        .btn-icon.ver {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn-icon.editar {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-icon.eliminar {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-icon.ver:hover {
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
        }

        .btn-icon.editar:hover {
            box-shadow: 0 8px 18px rgba(71, 85, 105, 0.14);
        }

        .btn-icon.eliminar:hover {
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.18);
        }

        .empty-card,
        .empty-filter-state {
            background: white;
            border-radius: 22px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            animation: fadeUp 0.45s ease both;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            width: 82px;
            height: 82px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 26px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: softFloat 3.2s ease-in-out infinite;
        }

        .empty-state-icon i {
            font-size: 2rem;
            color: white;
            margin: 0 !important;
        }

        .empty-state h3 {
            color: #475569;
            margin: 0 0 8px 0;
            font-weight: 900;
        }

        .empty-state p {
            color: var(--text-soft);
            margin: 0 0 20px 0;
            line-height: 1.5;
        }

        .empty-filter-state {
            display: none;
            padding: 44px 20px;
            text-align: center;
            border: 1px dashed #cbd5e1;
        }

        .empty-filter-state.is-visible {
            display: block;
        }

        .empty-filter-state i {
            font-size: 2rem;
            color: var(--text-soft);
            margin: 0 0 12px 0 !important;
        }

        .empty-filter-state h3 {
            margin: 0 0 8px 0;
            color: #475569;
            font-weight: 900;
        }

        .empty-filter-state p {
            margin: 0 0 18px 0;
            color: var(--text-soft);
        }

        .vehiculo-modal .header,
        #modal-foto .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            color: white !important;
            display: flex !important;
            align-items: center;
            gap: 10px;
        }

        .vehiculo-modal .header i,
        #modal-foto .header i {
            margin: 0 !important;
        }

        .modal-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-input,
        textarea.form-input,
        select.form-input {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            min-height: 44px;
            border-radius: 14px !important;
        }

        textarea.form-input {
            min-height: 86px;
        }

        .upload-area {
            width: 100%;
            min-height: 220px;
            border-radius: 18px;
            border: 2px dashed #cbd5e1;
            background: var(--soft-bg);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: var(--primary);
            background: #eff6ff;
        }

        .upload-label {
            width: 100%;
            cursor: pointer;
            display: block;
            margin: 0;
        }

        .upload-placeholder {
            min-height: 220px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .upload-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
        }

        .upload-icon i {
            margin: 0 !important;
            font-size: 1.45rem;
        }

        .upload-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: center;
        }

        .upload-text .primary {
            color: var(--text-main);
            font-weight: 900;
        }

        .upload-text .secondary,
        .upload-formats {
            color: var(--text-muted);
            font-size: 0.86rem;
            word-break: break-word;
        }

        .upload-formats {
            margin-top: 8px;
        }

        .upload-preview-container {
            width: 100%;
            padding: 20px;
            position: relative;
            text-align: center;
            animation: scaleFade 0.25s ease both;
        }

        #preview-content {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            position: relative;
            max-width: 100%;
        }

        .preview-image {
            max-width: 100%;
            width: auto;
            max-height: 220px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
            object-fit: contain;
            display: none;
        }

        .remove-preview-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: none;
            background: var(--danger);
            color: white;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 8px 18px rgba(239, 68, 68, 0.28);
            z-index: 10;
        }

        .remove-preview-btn i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .remove-preview-btn:hover {
            transform: scale(1.08);
            background: var(--danger-dark);
        }

        #foto-modal {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 14px;
            object-fit: contain;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
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

        @keyframes softFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-4px);
            }
        }

        @media (max-width: 992px) {
            .vehiculos-toolbar {
                grid-template-columns: 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
            }

            .vehiculo-card {
                grid-template-columns: 130px minmax(0, 1fr);
            }

            .vehiculo-actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
                border-top: 1px solid #f1f5f9;
                padding-top: 14px;
            }
        }

        @media (max-width: 768px) {
            .vehiculos-hero {
                border-radius: 0 0 24px 24px;
                margin: -8px -4px 18px;
                padding: 22px;
            }

            .vehiculos-hero-content {
                align-items: stretch;
            }

            .vehiculos-hero-left {
                align-items: flex-start;
            }

            .vehiculos-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
            }

            .vehiculos-title {
                font-size: 1.55rem;
            }

            .vehiculos-subtitle {
                font-size: 0.92rem;
            }

            .vehiculos-hero-action {
                width: 100%;
            }

            .vehiculos-hero-action .btn {
                width: 100%;
            }

            .vehiculos-toolbar {
                grid-template-columns: 1fr;
                padding: 16px;
                border-radius: 18px;
            }

            .filter-actions {
                grid-column: auto;
                justify-content: stretch;
            }

            .btn-filter-clear {
                width: 100%;
            }

            .vehiculo-card {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 16px;
                border-radius: 18px;
            }

            .vehiculo-card::before {
                width: 100%;
                height: 6px;
                inset: 0 0 auto 0;
            }

            .vehiculo-foto {
                width: 100%;
                height: 185px;
            }

            .vehiculo-main-row {
                justify-content: center;
            }

            .vehiculo-detalles {
                justify-content: center;
            }

            .vehiculo-actions {
                justify-content: center;
                width: 100%;
            }

            .modal-grid-2 {
                grid-template-columns: 1fr;
                gap: 0;
                margin-bottom: 0;
            }

            .vehiculo-modal .actions {
                display: flex !important;
                flex-direction: column-reverse !important;
                gap: 10px !important;
            }

            .vehiculo-modal .actions .button,
            .vehiculo-modal .actions button {
                width: 100% !important;
                margin: 0 !important;
            }

            .upload-placeholder {
                min-height: 190px;
                padding: 20px 16px;
            }

            .preview-image {
                max-height: 210px;
            }

            html,
            body {
                max-width: 100%;
                overflow-x: hidden !important;
            }

            .ui.dimmer {
                padding: 12px !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }

            #modal-vehiculo.ui.modal,
            #modal-foto.ui.modal {
                width: calc(100vw - 24px) !important;
                max-width: 420px !important;
                min-width: 0 !important;
                left: 50% !important;
                right: auto !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                transform: translateX(-50%) !important;
                box-sizing: border-box !important;
                border-radius: 18px !important;
                overflow: hidden !important;
            }

            #modal-vehiculo.ui.modal > .content,
            #modal-foto.ui.modal > .content {
                max-height: calc(100dvh - 170px) !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 16px !important;
                box-sizing: border-box !important;
                -webkit-overflow-scrolling: touch;
            }

            #modal-vehiculo.ui.modal > .header,
            #modal-foto.ui.modal > .header {
                padding: 16px !important;
                font-size: 1.05rem !important;
                line-height: 1.25 !important;
            }

            #modal-vehiculo.ui.modal > .actions,
            #modal-foto.ui.modal > .actions {
                padding: 12px 16px !important;
                display: flex !important;
                flex-direction: column-reverse !important;
                gap: 10px !important;
            }

            #modal-vehiculo.ui.modal > .actions .button,
            #modal-vehiculo.ui.modal > .actions button,
            #modal-foto.ui.modal > .actions .button,
            #modal-foto.ui.modal > .actions button {
                width: 100% !important;
                margin: 0 !important;
            }

            #modal-vehiculo *,
            #modal-foto * {
                box-sizing: border-box;
            }

            .ajs-modal {
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 12px !important;
                box-sizing: border-box !important;
            }

            .ajs-dialog {
                width: calc(100vw - 24px) !important;
                max-width: 420px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                box-sizing: border-box !important;
                border-radius: 16px !important;
            }

            .ajs-content {
                max-height: calc(100dvh - 170px) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .ajs-footer .ajs-buttons {
                display: flex !important;
                flex-direction: column-reverse !important;
                gap: 10px !important;
            }

            .ajs-footer .ajs-buttons .ajs-button {
                width: 100% !important;
                margin: 0 !important;
            }
        }

        @media (max-width: 576px) {
            .vehiculo-foto {
                height: 160px;
            }

            .vehiculo-placas {
                font-size: 1.05rem;
            }

            .vehiculo-marca {
                font-size: 0.9rem;
            }

            .vehiculo-detalles {
                gap: 8px;
                font-size: 0.8rem;
            }

            .btn-icon {
                width: 42px;
                height: 42px;
            }

            .upload-placeholder {
                min-height: 170px;
            }

            .upload-text .primary {
                font-size: 0.95rem;
            }

            .upload-text .secondary,
            .upload-formats {
                font-size: 0.85rem;
            }

            .upload-preview-container {
                padding: 14px;
            }

            .preview-image {
                max-height: 190px;
            }
        }

        @media (max-width: 420px) {
            #modal-vehiculo.ui.modal,
            #modal-foto.ui.modal {
                width: calc(100vw - 20px) !important;
                max-width: calc(100vw - 20px) !important;
            }

            .ajs-dialog {
                width: calc(100vw - 20px) !important;
                max-width: calc(100vw - 20px) !important;
            }

            .vehiculo-actions {
                flex-wrap: wrap;
            }
        }

        @media (hover: none) {
            .vehiculo-card:hover,
            .btn-icon:hover {
                transform: none;
            }

            .vehiculo-card:hover .vehiculo-foto img {
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

    <div class="vehiculos-page">
        <div class="vehiculos-hero">
            <div class="vehiculos-hero-content">
                <div class="vehiculos-hero-left">
                    <div class="vehiculos-hero-icon">
                        <i class="car icon"></i>
                    </div>

                    <div>
                        <h1 class="vehiculos-title">Vehículos</h1>
                        <p class="vehiculos-subtitle">Gestiona los vehículos registrados en tu cuenta</p>
                    </div>
                </div>

                <div class="vehiculos-hero-action">
                    <button class="btn btn-primary" onclick="abrirModalVehiculo()" type="button">
                        <i class="plus icon"></i>
                        Agregar vehículo
                    </button>
                </div>
            </div>
        </div>

        @if($vehiculos->isEmpty())
            <div class="empty-card">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="car icon"></i>
                    </div>

                    <h3>Sin vehículos registrados</h3>
                    <p>Agrega los vehículos que deseas registrar en el condominio.</p>

                    <button class="btn btn-primary" onclick="abrirModalVehiculo()" type="button">
                        <i class="plus icon"></i>
                        Registrar mi primer vehículo
                    </button>
                </div>
            </div>
        @else
            <div class="vehiculos-toolbar">
                <div class="filter-group">
                    <label class="filter-label" for="filtro-palabra">Buscar vehículo</label>

                    <div class="filter-control">
                        <span class="filter-icon">
                            <i class="search icon"></i>
                        </span>

                        <input
                            type="text"
                            id="filtro-palabra"
                            class="filter-input"
                            placeholder="Buscar por placas, marca, modelo, color, casa o residente..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="filtro-tipo">Tipo de vehículo</label>

                    <div class="filter-control">
                        <span class="filter-icon">
                            <i class="filter icon"></i>
                        </span>

                        <select id="filtro-tipo" class="filter-select">
                            <option value="todos">Todos los tipos</option>
                            <option value="automovil">Automóvil</option>
                            <option value="motocicleta">Motocicleta</option>
                            <option value="camioneta">Camioneta</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="button" class="btn btn-secondary btn-filter-clear" onclick="limpiarFiltrosVehiculos()">
                        <i class="times icon"></i>
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <p class="vehiculos-count" id="vehiculos-count"></p>

            <div class="vehiculos-list" id="vehiculos-list">
                @foreach($vehiculos as $vehiculo)
                    @php
                        $textoBusqueda = strtolower(trim(
                            ($vehiculo->placas ?? '') . ' ' .
                            ($vehiculo->marca ?? '') . ' ' .
                            ($vehiculo->modelo ?? '') . ' ' .
                            ($vehiculo->anio ?? '') . ' ' .
                            ($vehiculo->color ?? '') . ' ' .
                            ($vehiculo->tipo ?? '') . ' ' .
                            ($vehiculo->user->casa ?? '') . ' ' .
                            ($vehiculo->user->nombre ?? '')
                        ));

                        $tieneFotoVehiculo = $vehiculo->foto && file_exists(storage_path('app/public/'.$vehiculo->foto));
                    @endphp

                    <div
                        class="vehiculo-card"
                        id="vehiculo-{{ $vehiculo->id }}"
                        data-tipo="{{ $vehiculo->tipo }}"
                        data-search="{{ e($textoBusqueda) }}"
                    >
                        <div class="vehiculo-foto">
                            @if($tieneFotoVehiculo)
                                <img src="{{ asset('storage/'.$vehiculo->foto) }}" alt="Foto de {{ $vehiculo->marca }} {{ $vehiculo->modelo }}">
                            @else
                                <i class="car icon"></i>
                            @endif
                        </div>

                        <div class="vehiculo-info">
                            <div class="vehiculo-main-row">
                                <div class="vehiculo-placas">{{ $vehiculo->placas }}</div>

                                <span class="vehiculo-tipo {{ $vehiculo->tipo }}">
                                    @if($vehiculo->tipo === 'automovil')
                                        <i class="car icon"></i> Automóvil
                                    @elseif($vehiculo->tipo === 'motocicleta')
                                        <i class="motorcycle icon"></i> Motocicleta
                                    @elseif($vehiculo->tipo === 'camioneta')
                                        <i class="truck icon"></i> Camioneta
                                    @else
                                        <i class="question circle icon"></i> Otro
                                    @endif
                                </span>
                            </div>

                            <div class="vehiculo-marca">
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                @if($vehiculo->anio)
                                    - {{ $vehiculo->anio }}
                                @endif
                            </div>

                            <div class="vehiculo-detalles">
                                <span>
                                    <i class="home icon"></i>
                                    {{ $vehiculo->user->casa }}
                                </span>

                                <span>
                                    <i class="user icon"></i>
                                    {{ $vehiculo->user->nombre }}
                                </span>

                                @if($vehiculo->color)
                                    <span>
                                        <i class="paint brush icon"></i>
                                        {{ $vehiculo->color }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="vehiculo-actions">
                            @if($tieneFotoVehiculo)
                                <button class="btn-icon ver" onclick="verFoto('{{ asset('storage/'.$vehiculo->foto) }}')" title="Ver foto" type="button">
                                    <i class="eye icon"></i>
                                </button>
                            @endif

                            @if($vehiculo->user_id == auth()->user()->id)
                                @php
                                    $vehiculoData = [
                                        'id' => $vehiculo->id,
                                        'marca' => $vehiculo->marca,
                                        'modelo' => $vehiculo->modelo,
                                        'anio' => $vehiculo->anio ?? '',
                                        'color' => $vehiculo->color ?? '',
                                        'placas' => $vehiculo->placas,
                                        'tipo' => $vehiculo->tipo,
                                        'observaciones' => $vehiculo->observaciones ?? '',
                                    ];
                                @endphp

                                <button
                                    class="btn-icon editar"
                                    type="button"
                                    onclick='editarVehiculo({!! json_encode($vehiculoData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) !!})'
                                    title="Editar"
                                >
                                    <i class="edit icon"></i>
                                </button>

                                <button class="btn-icon eliminar" onclick="eliminarVehiculo({{ $vehiculo->id }})" title="Eliminar" type="button">
                                    <i class="trash icon"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="empty-filter-state" id="empty-filter-state">
                <i class="search icon"></i>
                <h3>No se encontraron vehículos</h3>
                <p>Intenta con otra palabra o cambia el tipo de vehículo.</p>

                <button type="button" class="btn btn-secondary" onclick="limpiarFiltrosVehiculos()">
                    <i class="times icon"></i>
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>
</x-app-layout>

<div class="ui modal vehiculo-modal" id="modal-vehiculo">
    <div class="header">
        <i class="car icon" id="modal-icono"></i>
        <span id="modal-titulo">Registrar Vehículo</span>
    </div>

    <div class="content">
        <form id="form-vehiculo" enctype="multipart/form-data">
            @csrf

            <input type="hidden" id="vehiculo-id" value="">
            <input type="hidden" id="vehiculo-method" value="POST">

            <div class="modal-grid-2">
                <div class="form-group">
                    <label class="form-label">Marca *</label>
                    <input type="text" id="vehiculo-marca" class="form-input" placeholder="Ej. Toyota, Honda" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Modelo *</label>
                    <input type="text" id="vehiculo-modelo" class="form-input" placeholder="Ej. Corolla, Civic" required>
                </div>
            </div>

            <div class="modal-grid-2">
                <div class="form-group">
                    <label class="form-label">Año</label>
                    <input type="text" id="vehiculo-anio" class="form-input" placeholder="Ej. 2023">
                </div>

                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="text" id="vehiculo-color" class="form-input" placeholder="Ej. Rojo, Blanco">
                </div>
            </div>

            <div class="modal-grid-2">
                <div class="form-group">
                    <label class="form-label">Placas *</label>
                    <input type="text" id="vehiculo-placas" class="form-input" placeholder="ABC-1234" style="text-transform: uppercase;" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de vehículo *</label>

                    <select id="vehiculo-tipo" class="form-input" required>
                        <option value="">Seleccionar</option>
                        <option value="automovil">Automóvil</option>
                        <option value="motocicleta">Motocicleta</option>
                        <option value="camioneta">Camioneta</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Observaciones</label>
                <textarea id="vehiculo-observaciones" class="form-input" rows="2" placeholder="Información adicional..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto del vehículo (opcional)</label>

                <div class="upload-area" id="upload-area-foto">
                    <input type="file" name="foto" id="foto-input" accept="image/jpeg,image/png,image/webp" hidden>

                    <label for="foto-input" class="upload-label">
                        <div class="upload-placeholder" id="upload-placeholder">
                            <div class="upload-icon">
                                <i class="camera icon"></i>
                            </div>

                            <div class="upload-text">
                                <span class="primary">Arrastra la imagen aquí</span>
                                <span class="secondary">o haz clic para seleccionar</span>
                            </div>

                            <div class="upload-formats">JPG, PNG o WEBP (máx 5MB)</div>
                        </div>

                        <div id="upload-preview" class="upload-preview-container" style="display: none;">
                            <div id="preview-content">
                                <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar">
                                    <i class="times icon"></i>
                                </button>

                                <img
                                    id="preview-image"
                                    alt="Vista previa del vehículo"
                                    class="preview-image"
                                >
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <div class="actions">
        <div class="ui black deny button">Cancelar</div>

        <button class="ui blue button" id="btn-guardar-vehiculo" onclick="guardarVehiculo()">
            <i class="save icon"></i>
            Guardar
        </button>
    </div>
</div>

<div class="ui modal" id="modal-foto">
    <div class="header">
        <i class="image icon"></i>
        Foto del Vehículo
    </div>

    <div class="content" style="text-align: center; padding: 20px;">
        <img id="foto-modal" alt="Foto del vehículo">
    </div>

    <div class="actions">
        <div class="ui black deny button">Cerrar</div>
    </div>
</div>

<script>
    const BASE_URL = "{{ url('/') }}";

    function normalizarTexto(texto) {
        return (texto || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function aplicarFiltrosVehiculos() {
        const palabra = normalizarTexto(document.getElementById('filtro-palabra')?.value || '');
        const tipo = document.getElementById('filtro-tipo')?.value || 'todos';
        const cards = document.querySelectorAll('.vehiculo-card');
        const emptyState = document.getElementById('empty-filter-state');
        const countText = document.getElementById('vehiculos-count');

        let visibles = 0;

        cards.forEach(card => {
            const texto = normalizarTexto(card.dataset.search || '');
            const tipoCard = card.dataset.tipo || '';

            const coincidePalabra = palabra === '' || texto.includes(palabra);
            const coincideTipo = tipo === 'todos' || tipoCard === tipo;

            if (coincidePalabra && coincideTipo) {
                card.classList.remove('is-hidden');
                visibles++;
            } else {
                card.classList.add('is-hidden');
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('is-visible', visibles === 0);
        }

        if (countText) {
            const total = cards.length;

            if (total === 1) {
                countText.textContent = visibles === 1 ? 'Mostrando 1 vehículo.' : 'No hay vehículos visibles.';
            } else {
                countText.textContent = `Mostrando ${visibles} de ${total} vehículos.`;
            }
        }
    }

    function limpiarFiltrosVehiculos() {
        const filtroPalabra = document.getElementById('filtro-palabra');
        const filtroTipo = document.getElementById('filtro-tipo');

        if (filtroPalabra) {
            filtroPalabra.value = '';
        }

        if (filtroTipo) {
            filtroTipo.value = 'todos';
        }

        aplicarFiltrosVehiculos();
    }

    function limpiarPreviewVehiculo() {
        const fileInput = document.getElementById('foto-input');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const uploadPreview = document.getElementById('upload-preview');
        const previewImage = document.getElementById('preview-image');
        const removeBtn = document.getElementById('remove-preview-btn');

        if (fileInput) {
            fileInput.value = '';
        }

        if (previewImage) {
            previewImage.removeAttribute('src');
            previewImage.style.display = 'none';
        }

        if (removeBtn) {
            removeBtn.style.display = 'none';
        }

        if (uploadPreview) {
            uploadPreview.style.display = 'none';
        }

        if (uploadPlaceholder) {
            uploadPlaceholder.style.display = 'flex';
        }
    }

    function abrirModalVehiculo() {
        $('#vehiculo-id').val('');
        $('#vehiculo-method').val('POST');
        $('#modal-titulo').text('Registrar Vehículo');
        $('#modal-icono').removeClass('edit').addClass('plus');
        $('#form-vehiculo')[0].reset();

        limpiarPreviewVehiculo();

        $('#btn-guardar-vehiculo').html('<i class="save icon"></i> Registrar');
        $('#modal-vehiculo').modal('show');

        setTimeout(function() {
            $('#modal-vehiculo .content').scrollTop(0);
        }, 100);
    }

    function editarVehiculo(vehiculo) {
        $('#vehiculo-id').val(vehiculo.id || '');
        $('#vehiculo-method').val('PUT');
        $('#modal-titulo').text('Editar Vehículo');
        $('#modal-icono').removeClass('plus').addClass('edit');

        $('#vehiculo-marca').val(vehiculo.marca || '');
        $('#vehiculo-modelo').val(vehiculo.modelo || '');
        $('#vehiculo-anio').val(vehiculo.anio || '');
        $('#vehiculo-color').val(vehiculo.color || '');
        $('#vehiculo-placas').val(vehiculo.placas || '');
        $('#vehiculo-tipo').val(vehiculo.tipo || '');
        $('#vehiculo-observaciones').val(vehiculo.observaciones || '');

        limpiarPreviewVehiculo();

        $('#btn-guardar-vehiculo').html('<i class="save icon"></i> Actualizar');
        $('#modal-vehiculo').modal('show');

        setTimeout(function() {
            $('#modal-vehiculo .content').scrollTop(0);
        }, 100);
    }

    function verFoto(url) {
        const fotoModal = document.getElementById('foto-modal');

        if (fotoModal) {
            fotoModal.src = url;
        }

        $('#modal-foto').modal('show');
    }

    function eliminarVehiculo(id) {
        alertify.confirm(
            '¿Eliminar vehículo?',
            '¿Estás seguro de que deseas eliminar este vehículo?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(() => {
                    $.ajax({
                        url: `${BASE_URL}/usuario/vehiculo/eliminar/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.success(res.message);

                            $(`#vehiculo-${id}`).fadeOut(300, function() {
                                $(this).remove();

                                if ($('.vehiculo-card').length === 0) {
                                    location.reload();
                                } else {
                                    aplicarFiltrosVehiculos();
                                }
                            });
                        },
                        error: function() {
                            alertify.error('Error al eliminar el vehículo');
                        },
                        complete: function() {
                            ocultarLoaderPantalla();
                        }
                    });
                }, 100);
            },
            function() {}
        );
    }

    function guardarVehiculo() {
        const id = $('#vehiculo-id').val();
        const isEdit = id !== '';
        const url = isEdit ? `${BASE_URL}/usuario/vehiculo/actualizar/${id}` : `${BASE_URL}/usuario/vehiculo/guardar`;

        const marca = $('#vehiculo-marca').val().trim();
        const modelo = $('#vehiculo-modelo').val().trim();
        const placas = $('#vehiculo-placas').val().trim();
        const tipo = $('#vehiculo-tipo').val();

        if (!marca || !modelo || !placas || !tipo) {
            alertify.error('Completa los campos obligatorios.');
            return;
        }

        const formData = new FormData();

        formData.append('_token', '{{ csrf_token() }}');
        formData.append('marca', marca);
        formData.append('modelo', modelo);
        formData.append('anio', $('#vehiculo-anio').val().trim());
        formData.append('color', $('#vehiculo-color').val().trim());
        formData.append('placas', placas.toUpperCase());
        formData.append('tipo', tipo);
        formData.append('observaciones', $('#vehiculo-observaciones').val().trim());

        const fotoFile = $('#foto-input')[0].files[0];

        if (fotoFile) {
            formData.append('foto', fotoFile);
        }

        const btn = $('#btn-guardar-vehiculo');
        btn.addClass('loading disabled');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                alertify.success(res.message);
                $('#modal-vehiculo').modal('hide');

                setTimeout(() => location.reload(), 500);
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error al guardar';
                alertify.error(message);
            },
            complete: function() {
                btn.removeClass('loading disabled');
            }
        });
    }

    function validarYMostrarPreview(file) {
        const maxSize = 5 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        const fileInput = document.getElementById('foto-input');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const uploadPreview = document.getElementById('upload-preview');
        const previewImage = document.getElementById('preview-image');
        const removeBtn = document.getElementById('remove-preview-btn');

        if (!file) {
            limpiarPreviewVehiculo();
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alertify.error('Solo se permiten imágenes JPG, PNG o WEBP');
            limpiarPreviewVehiculo();
            return;
        }

        if (file.size > maxSize) {
            alertify.error('La imagen no puede superar los 5MB');
            limpiarPreviewVehiculo();
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            removeBtn.style.display = 'flex';
            uploadPlaceholder.style.display = 'none';
            uploadPreview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filtroPalabra = document.getElementById('filtro-palabra');
        const filtroTipo = document.getElementById('filtro-tipo');

        if (filtroPalabra) {
            filtroPalabra.addEventListener('input', aplicarFiltrosVehiculos);
        }

        if (filtroTipo) {
            filtroTipo.addEventListener('change', aplicarFiltrosVehiculos);
        }

        aplicarFiltrosVehiculos();

        const fileInput = document.getElementById('foto-input');
        const removeBtn = document.getElementById('remove-preview-btn');
        const uploadArea = document.getElementById('upload-area-foto');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                validarYMostrarPreview(this.files[0]);
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                limpiarPreviewVehiculo();
            });
        }

        if (uploadArea && fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, function() {
                    uploadArea.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, function() {
                    uploadArea.classList.remove('dragover');
                }, false);
            });

            uploadArea.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    validarYMostrarPreview(files[0]);
                }
            });
        }

        $('#modal-vehiculo').modal({
            observeChanges: true,
            detachable: false,
            autofocus: false,
            onVisible: function() {
                setTimeout(function() {
                    $('#modal-vehiculo .content').scrollTop(0);
                }, 50);
            },
            onHidden: function() {
                limpiarPreviewVehiculo();
            }
        });

        $('#modal-foto').modal({
            detachable: false,
            autofocus: false,
            onHidden: function() {
                const fotoModal = document.getElementById('foto-modal');

                if (fotoModal) {
                    fotoModal.removeAttribute('src');
                }
            }
        });
    });
</script>