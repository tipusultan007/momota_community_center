<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-1">পাসওয়ার্ড নিশ্চিত করুন</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8 px-2">
            এটি অ্যাপ্লিকেশনের একটি নিরাপদ এলাকা। অনুগ্রহ করে চালিয়ে যাওয়ার আগে আপনার পাসওয়ার্ড নিশ্চিত করুন।
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-primary-container">পাসওয়ার্ড</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="আপনার পাসওয়ার্ড দিন"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-3 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('password') border-error @enderror">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    নিশ্চিত করুন
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
