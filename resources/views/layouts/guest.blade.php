<!doctype html>
<html lang="bn" dir="ltr" class="h-full bg-[#f7f9fb]">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ config('app.name', 'Majestic Suite') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN (Matches Landing Page) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
          tailwind.config = {
            darkMode: "class",
            theme: {
              extend: {
                colors: {
                  "inverse-primary": "#bec6e0",
                  "outline-variant": "#c6c6cd",
                  "on-tertiary-fixed": "#241a00",
                  "secondary-fixed": "#6ffbbe",
                  "on-error": "#ffffff",
                  "on-secondary-fixed-variant": "#005236",
                  "on-error-container": "#93000a",
                  "surface-container-high": "#e6e8ea",
                  "inverse-on-surface": "#eff1f3",
                  "on-primary-fixed-variant": "#3f465c",
                  "tertiary-container": "#cba72f",
                  "surface-bright": "#f7f9fb",
                  "error": "#ba1a1a",
                  "on-secondary-fixed": "#002113",
                  "surface-container-low": "#f2f4f6",
                  "tertiary-fixed": "#ffe088",
                  "on-primary": "#ffffff",
                  "on-surface": "#191c1e",
                  "tertiary-fixed-dim": "#e9c349",
                  "surface-container-highest": "#e0e3e5",
                  "background": "#f7f9fb",
                  "surface": "#f7f9fb",
                  "on-primary-container": "#7c839b",
                  "surface-variant": "#e0e3e5",
                  "on-tertiary-container": "#4e3d00",
                  "secondary-container": "#6cf8bb",
                  "primary": "#000000",
                  "surface-dim": "#d8dadc",
                  "secondary-fixed-dim": "#4edea3",
                  "inverse-surface": "#2d3133",
                  "primary-fixed-dim": "#bec6e0",
                  "surface-container-lowest": "#ffffff",
                  "error-container": "#ffdad6",
                  "primary-container": "#131b2e",
                  "outline": "#76777d",
                  "primary-fixed": "#dae2fd",
                  "on-background": "#191c1e",
                  "secondary": "#006c49",
                  "on-primary-fixed": "#131b2e",
                  "on-secondary": "#ffffff",
                  "on-secondary-container": "#00714d",
                  "tertiary": "#735c00",
                  "surface-tint": "#565e74",
                  "on-tertiary-fixed-variant": "#574500",
                  "on-surface-variant": "#45464d",
                  "surface-container": "#eceef0",
                  "on-tertiary": "#ffffff"
                },
                fontFamily: {
                  "headline": ["Anek Bangla", "Inter", "sans-serif"],
                  "body": ["Anek Bangla", "Inter", "sans-serif"],
                },
              },
            },
          }
    </script>
    <style>
      body { font-family: 'Anek Bangla', 'Inter', sans-serif; }
      .glass-effect {
          background: rgba(255, 255, 255, 0.7);
          backdrop-filter: blur(20px);
          border: 1px solid rgba(255, 255, 255, 0.3);
      }
    </style>
  </head>
  <body class="h-full selection:bg-secondary-container antialiased">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden bg-background">
        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-secondary/10 rounded-full blur-[100px] -z-10"></div>
        <div class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-[500px] h-[500px] bg-primary-container/10 rounded-full blur-[100px] -z-10"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4">
            <a href="/" class="flex flex-col items-center group">
                <span class="text-4xl font-black tracking-tighter text-primary-container mb-1 group-hover:text-secondary transition-colors duration-300">
                    {{ config('app.name', 'PranganHQ') }}
                </span>
                <span class="text-[10px] uppercase tracking-[0.3em] text-on-surface-variant font-bold">Event Management Platform</span>
            </a>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[440px] relative z-10 px-4">
            <div class="glass-effect py-10 px-6 shadow-2xl shadow-primary-container/10 rounded-3xl sm:px-12 border border-white/50">
                {{ $slot }}
            </div>
        </div>
    </div>
  </body>
</html>
