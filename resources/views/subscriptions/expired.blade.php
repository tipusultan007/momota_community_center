<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>সাবস্ক্রিপশন শেষ হয়ে গেছে</title>
    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root { --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; }
        body { font-feature-settings: "cv03", "cv04", "cv11"; }
    </style>
</head>
<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">৪0২</div>
                <p class="empty-title">আপনার সাবস্ক্রিপশন শেষ হয়ে গেছে!</p>
                <p class="empty-subtitle text-muted">
                    দুঃখিত, আপনার অ্যাকাউন্টের মেয়াদ শেষ হয়ে গেছে। দয়া করে আপনার প্ল্যান রিনিউ করুন অথবা নতুন একটি প্ল্যান বেছে নিন।
                </p>
                <div class="empty-action">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                        প্ল্যান রিনিউ করুন
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
