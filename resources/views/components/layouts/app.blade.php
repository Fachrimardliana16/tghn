<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js must load before Livewire -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Theme init: apply saved theme before paint to avoid flash -->
    <script>
        (function() {
            const t = localStorage.getItem('theme') || 'light';
            document.documentElement.dataset.theme = t;
        })();
    </script>

    <style>
        /* =============================================
           SMOOTH TRANSITION FOR THEME SWITCHING
        ============================================= */
        body, div, section, header, footer, nav, main, aside,
        table, thead, tbody, tr, th, td, ul, li, a, p, h1, h2, h3, h4,
        button, input, select, textarea, span, svg, canvas {
            transition: background-color 0.25s ease, color 0.25s ease,
                        border-color 0.25s ease, box-shadow 0.25s ease !important;
        }

        /* =============================================
           DARK MODE OVERRIDES
        ============================================= */

        /* --- Page background gradient --- */
        [data-theme="dark"] .from-gray-50 { --tw-gradient-from: #0f172a !important; }
        [data-theme="dark"] .to-gray-100  { --tw-gradient-to:   #1e293b !important; }
        [data-theme="dark"] .from-gray-100{ --tw-gradient-from: #1e293b !important; }

        /* --- Solid backgrounds --- */
        [data-theme="dark"] .bg-white     { background-color: #1e293b !important; }
        [data-theme="dark"] .bg-gray-50   { background-color: #0f172a !important; }
        [data-theme="dark"] .bg-gray-100  { background-color: #334155 !important; }

        /* --- Text colors --- */
        [data-theme="dark"] .text-gray-900 { color: #f1f5f9 !important; }
        [data-theme="dark"] .text-gray-800 { color: #e2e8f0 !important; }
        [data-theme="dark"] .text-gray-700 { color: #cbd5e1 !important; }
        [data-theme="dark"] .text-gray-600 { color: #94a3b8 !important; }
        [data-theme="dark"] .text-gray-500 { color: #64748b  !important; }
        [data-theme="dark"] .text-gray-400 { color: #475569  !important; }

        /* --- Borders --- */
        [data-theme="dark"] .border-gray-100 { border-color: #334155 !important; }
        [data-theme="dark"] .border-gray-200 { border-color: #334155 !important; }
        [data-theme="dark"] .border-gray-300 { border-color: #475569 !important; }

        /* --- Table dividers --- */
        [data-theme="dark"] .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #334155 !important;
        }

        /* --- Hover states --- */
        [data-theme="dark"] .hover\:bg-gray-50:hover  { background-color: #263348 !important; }
        [data-theme="dark"] .hover\:bg-gray-100:hover { background-color: #1e293b !important; }
        [data-theme="dark"] .hover\:bg-gray-200:hover { background-color: #334155 !important; }

        /* --- Ring/outline --- */
        [data-theme="dark"] .ring-black { --tw-ring-color: rgba(148,163,184,0.15) !important; }

        /* --- Shadows get deeper on dark --- */
        [data-theme="dark"] .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5),
                        0 10px 10px -5px rgba(0,0,0,0.3) !important;
        }
        [data-theme="dark"] .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.4),
                        0 2px 4px -1px rgba(0,0,0,0.2) !important;
        }

        /* --- Green alert (session message) --- */
        [data-theme="dark"] .from-green-50 { --tw-gradient-from: #052e16 !important; }
        [data-theme="dark"] .to-green-100  { --tw-gradient-to:   #064e3b !important; }
        [data-theme="dark"] .text-green-800{ color: #6ee7b7 !important; }
        [data-theme="dark"] .border-green-500 { border-color: #10b981 !important; }

        /* --- Red alert (validation errors) --- */
        [data-theme="dark"] .bg-red-50    { background-color: #450a0a !important; }
        [data-theme="dark"] .border-red-200{ border-color: #991b1b !important; }
        [data-theme="dark"] .text-red-700 { color: #fca5a5 !important; }

        /* --- Amber section (edit user password area) --- */
        [data-theme="dark"] .bg-amber-50   { background-color: #451a03 !important; }
        [data-theme="dark"] .border-amber-200{ border-color: #92400e !important; }
        [data-theme="dark"] .text-amber-700 { color: #fcd34d !important; }

        /* --- Blue badges --- */
        [data-theme="dark"] .bg-blue-100  { background-color: #1e3a5f !important; }
        [data-theme="dark"] .text-blue-800 { color: #93c5fd !important; }

        /* --- Indigo accents (user management) --- */
        [data-theme="dark"] .bg-indigo-100  { background-color: #1e1b4b !important; }
        [data-theme="dark"] .text-indigo-600 { color: #a5b4fc !important; }

        /* --- Settings dropdown background --- */
        [data-theme="dark"] #themeLabel { color: #e2e8f0 !important; }

        /* --- Form inputs in user management pages --- */
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="password"] {
            background-color: #0f172a !important;
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        [data-theme="dark"] input::placeholder {
            color: #64748b !important;
        }

        /* --- Cancel button in user pages --- */
        [data-theme="dark"] .bg-gray-100.text-gray-700 {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        /* --- Table rows background fix --- */
        [data-theme="dark"] tbody.bg-white { background-color: #1e293b !important; }
        [data-theme="dark"] thead.bg-gray-50 { background-color: #0f172a !important; }

        /* --- Rank badges in top customers table keep their gradient but adjust border --- */
        [data-theme="dark"] .from-blue-400.to-blue-600  { border: none; }
        [data-theme="dark"] .from-yellow-400.to-yellow-600 { border: none; }

        /* --- Chart card h3 accent bars (colored spans) stay vivid --- */
        /* no override needed: they use from-blue-500 etc which are fine */

        /* --- Scrollbar in dark mode --- */
        [data-theme="dark"] ::-webkit-scrollbar { background: #0f172a; }
        [data-theme="dark"] ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>

    @livewireStyles
</head>
<body class="antialiased">
    {{ $slot }}

    @livewireScripts
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</body>
</html>