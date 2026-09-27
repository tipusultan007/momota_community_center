<x-guest-layout>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary-container text-center mb-1">নতুন অ্যাকাউন্ট তৈরি করুন</h2>
        <p class="text-center text-sm text-on-surface-variant mb-8">আজই আপনার যাত্রা শুরু করুন</p>

        <form action="{{ route('register') }}" method="post" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-semibold text-primary-container">আপনার নাম</label>
                <div class="mt-1">
                    <input id="name" name="name" type="text" autocomplete="name" required placeholder="পুরো নাম লিখুন" value="{{ old('name') }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('name') border-error @enderror">
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="business_name" class="block text-sm font-semibold text-primary-container">প্রতিষ্ঠানের নাম (কনভেনশন হল/সেন্টার)</label>
                <div class="mt-1">
                    <input id="business_name" name="business_name" type="text" required placeholder="প্রতিষ্ঠানের নাম লিখুন" value="{{ old('business_name') }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('business_name') border-error @enderror">
                </div>
                @error('business_name')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-primary-container">মোবাইল নম্বর</label>
                <div class="mt-1">
                    <input id="phone" name="phone" type="tel" autocomplete="tel" placeholder="আপনার মোবাইল নম্বর (যেমন: 017xxxxxxxx)" value="{{ old('phone') }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('phone') border-error @enderror">
                </div>
                @error('phone')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-primary-container">ইমেল ঠিকানা</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required placeholder="আপনার ইমেল দিন" value="{{ old('email') }}"
                        class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('email') border-error @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-primary-container">পাসওয়ার্ড</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="পাসওয়ার্ড দিন"
                            class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm @error('password') border-error @enderror">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-primary-container">নিশ্চিত করুন</label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="আবার দিন"
                            class="block w-full rounded-lg border-surface-container bg-surface-bright px-4 py-2.5 text-primary-container shadow-sm focus:border-secondary focus:ring-secondary sm:text-sm">
                    </div>
                </div>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-error">{{ $message }}</p>
            @enderror

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 rounded border-surface-container text-secondary focus:ring-secondary">
                <label for="terms" class="ml-2 block text-sm text-on-surface-variant">
                    আমি <a href="#" class="font-bold text-secondary hover:text-secondary-fixed-dim">শর্তাবলী ও নীতিমালা</a> মেনে নিচ্ছি।
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="flex w-full justify-center rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 hover:bg-on-secondary-container transition-all active:scale-95 duration-150">
                    নিবন্ধন সম্পন্ন করুন
                </button>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-on-surface-variant line-height-[1.6]">
            ইতিমধ্যেই অ্যাকাউন্ট আছে? 
            <a href="{{ route('login') }}" class="font-bold text-secondary hover:text-secondary-fixed-dim">প্রবেশ করুন</a>
        </div>
    </div>
</x-guest-layout>
