<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-1">পাসওয়ার্ড পরিবর্তন করুন</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8">আপনার নতুন পাসওয়ার্ড সেট করুন</p>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-primary-container">ইমেল ঠিকানা</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required placeholder="আপনার ইমেল দিন" value="{{ old('email', $request->email) }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('email') border-error @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-primary-container">নতুন পাসওয়ার্ড</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="নতুন পাসওয়ার্ড দিন"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('password') border-error @enderror">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-primary-container">পাসওয়ার্ড নিশ্চিত করুন</label>
                <div class="mt-1">
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="আবার নতুন পাসওয়ার্ড দিন"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('password_confirmation') border-error @enderror">
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    পাসওয়ার্ড পরিবর্তন সম্পন্ন করুন
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
