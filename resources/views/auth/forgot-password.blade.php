<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-2">পাসওয়ার্ড পুনরুদ্ধার</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8 leading-relaxed px-2">
            পাসওয়ার্ড ভুলে গেছেন? সমস্যা নেই। আপনার ইমেল ঠিকানাটি দিন এবং আমরা আপনাকে একটি পাসওয়ার্ড রিসেট লিঙ্ক পাঠিয়ে দেব।
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-primary-container">ইমেল ঠিকানা</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required placeholder="আপনার ইমেল দিন" value="{{ old('email') }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-3 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('email') border-error @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    পাসওয়ার্ড রিসেট লিঙ্ক পাঠান
                </button>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-on-surface-variant">
            আবার ফিরে যেতে চান? 
            <a href="{{ route('login') }}" class="font-bold text-secondary hover:text-secondary-fixed-dim">লগইন করুন</a>
        </div>
    </div>
</x-guest-layout>
