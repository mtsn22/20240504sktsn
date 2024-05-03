<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{--
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div> --}}

        <!-- Username -->
        <div>
            <p class="text-center text-lg"><strong>S</strong>istem <strong>I</strong>nformasi <strong>AKAD</strong>emik
            </p>
            <p class="text-center text-lg"><strong>Ma'had Ta'dzimussunnah Sine Ngawi</strong></p>
            </br>

            <x-input-label for="username" :value="__('Username')" />
            <x-text-input placeholder="Bagi walisantri silakan isi nomor KK" id="username" class="block mt-1 w-full"
                type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-between justify-between mt-4">
            <div class="grid grid-cols-1">

                <p class="text-xs">WA Admin untuk bantuan dan request password</p>
                </br>
                <a aria-label="Chat on WhatsApp"
                    href="https://wa.me/6282210862400?text=Bismillah,+mohon+informasi+password+atas+nomor+KK+:+(isi+nomor+KK)"
                    target="blank">
                    <img alt="Chat on WhatsApp" src="WhatsAppButtonWhiteSmall.png" width="175" />
                </a>
            </div>


            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
