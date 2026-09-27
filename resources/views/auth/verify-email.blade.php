<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-1">ইমেল যাচাই করুন</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8 px-2">
            নিবন্ধনের জন্য ধন্যবাদ! শুরু করার আগে, আপনি কি আপনার ইমেল ঠিকানাটি যাচাই করতে পারেন? আমরা আপনাকে একটি লিঙ্ক পাঠিয়েছি। যদি আপনি ইমেলটি না পেয়ে থাকেন, তবে আমরা সানন্দে আপনাকে আরেকটি পাঠিয়ে দেব।
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-lg bg-secondary/10 p-4 text-sm font-medium text-secondary text-center">
                নিবন্ধনের সময় আপনি যে ইমেল ঠিকানাটি দিয়েছিলেন, সেখানে একটি নতুন যাচাইকরণ লিঙ্ক পাঠানো হয়েছে।
            </div>
        @endif

        <div class="mt-4 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    যাচাইকরণ ইমেল পুনরায় পাঠান
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center">
                @csrf
                <button type="submit" class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors underline underline-offset-4 decoration-secondary/30">
                    লগআউট
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
