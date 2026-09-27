<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-1">স্বাগতম ফিরে এসেছেন</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8">আপনার অ্যাকাউন্টে লগইন করুন</p>
        
        <form action="{{ route('login') }}" method="post" class="space-y-6">
            @csrf
            <div>
                <label for="login" class="block text-sm font-semibold text-primary-container">মোবাইল নম্বর / ইমেল</label>
                <div class="mt-1">
                    <input id="login" name="login" type="text" autocomplete="username" required placeholder="আপনার ফোন নম্বর বা ইমেল দিন" value="{{ old('login', old('email')) }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-3 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @if($errors->has('login') || $errors->has('email')) border-error @endif">
                </div>
                @error('login')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
                @if(!$errors->has('login'))
                    @error('email')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-primary-container">পাসওয়ার্ড</label>
                    <div class="text-sm">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-bold text-secondary hover:text-secondary-fixed-dim">পাসওয়ার্ড ভুলে গেছেন?</a>
                        @endif
                    </div>
                </div>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="আপনার পাসওয়ার্ড দিন"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-3 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('password') border-error @enderror">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-surface-container text-secondary focus:ring-secondary">
                <label for="remember_me" class="ml-2 block text-sm text-on-surface-variant">আমাকে মনে রাখুন</label>
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    প্রবেশ করুন
                </button>
            </div>
        </form>

        <div class="mt-10">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-surface-container"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-2 text-on-surface-variant font-medium">অথবা</span>
                </div>
            </div>

            <div class="mt-6 text-center text-sm text-on-surface-variant">
                এখনও অ্যাকাউন্ট নেই? 
                <a href="{{ route('register') }}" class="font-bold text-secondary hover:text-secondary-fixed-dim">নতুন অ্যাকাউন্ট খুলুন</a>
            </div>
        </div>
    </div>
</x-guest-layout>
