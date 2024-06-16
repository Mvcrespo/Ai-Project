<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CineMagic</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts AND CSS Files -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-800">

        <!-- Navigation Menu -->
        <nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative flex flex-col sm:flex-row px-6 sm:px-0 grow justify-between">
                    <!-- Logo -->
                    <div class="shrink-0 -ms-4">
                        <a href="{{ route('movies.high') }}">
                            <div class="h-16 w-40 relative">
                                <div class="absolute inset-0 bg-center bg-no-repeat bg-[url('../img/CineMagic_light_mode.svg')] dark:bg-[url('../img/CineMagic_drak_mode.svg')] bg-contain"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Menu Items -->
                    <div id="menu-container" class="grow flex flex-col sm:flex-row items-stretch invisible h-0 sm:visible sm:h-auto">
                        <!-- Menu Item: Filmes -->
                        <x-menus.menu-item
                            content="Filmes"
                            href="{{ route('movies.high') }}"
                            selected="{{ request()->is('/')}}"
                        />

                        <div class="grow"></div>

                        <div class="flex items-center space-x-3">
                            <!-- Dark mode toggle -->
                            <div>
                                <input type="checkbox" name="light-switch" id="light-switch" class="light-switch sr-only" />
                                <label id="theme-toggle" class="flex items-center justify-center cursor-pointer w-8 h-8 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600/80 rounded-full" for="light-switch">
                                    <svg class="w-4 h-4 dark:hidden" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path class="fill-current text-slate-400" d="M7 0h2v2H7V0Zm5.88 1.637 1.414 1.415-1.415 1.413-1.414-1.414 1.415-1.414ZM14 7h2v2h-2V7Zm-1.05 7.433-1.415-1.414 1.414-1.414 1.415 1.413-1.414 1.415ZM7 14h2v2H7v-2Zm-4.02.363L1.566 12.95l1.415-1.414 1.414 1.415-1.415 1.413ZM0 7h2v2H0V7Zm3.05-5.293L4.465 3.12 3.05 4.535 1.636 3.121 3.05 1.707Z" />
                                        <path class="fill-current text-slate-500" d="M8 4C5.8 4 4 5.8 4 8s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4Z" />
                                    </svg>
                                    <svg class="w-4 h-4 hidden dark:block" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path class="fill-current text-slate-400" d="M6.2 2C3.2 2.8 1 5.6 1 8.9 1 12.8 4.2 16 8.1 16c3.3 0 6-2.2 6.9-5.2C9.7 12.2 4.8 7.3 6.2 2Z" />
                                        <path class="fill-current text-slate-500" d="M12.5 6a.625.625 0 0 1-.625-.625 1.252 1.252 0 0 0-1.25-1.25.625.625 0 1 1 0-1.25 1.252 1.252 0 0 0 1.25-1.25.625.625 0 1 1 1.25 0c.001.69.56 1.249 1.25 1.25a.625.625 0 1 1 0 1.25c-.69.001-1.249.56-1.25 1.25A.625.625 0 0 1 12.5 6Z" />
                                    </svg>
                                    <span class="sr-only">Switch to light / dark version</span>
                                </label>
                            </div>
                        </div>

                        <!-- Menu Item: Cart -->
                        <x-menus.cart
                            href="{{ route('cart.show') }}"
                            selectable="0"
                            selected="1"
                            total="{{ session('cart') ? session('cart')->count() : 0 }}"/>

                        @auth
                            <x-menus.submenu
                                selectable="0"
                                uniqueName="submenu_user"
                            >
                                <x-slot:content>
                                    <div class="pe-1">
                                        <img src="{{ Auth::user()->photoFullUrl }}" class="w-11 h-11 min-w-11 min-h-11 rounded-full">
                                    </div>
                                    <div class="ps-1 sm:max-w-[calc(100vw-39rem)] md:max-w-[calc(100vw-41rem)] lg:max-w-[calc(100vw-46rem)] xl:max-w-[34rem] truncate">
                                        {{ Auth::user()->name }}
                                    </div>
                                </x-slot>

                                <hr>
                                @if(Auth::user()->type !== 'E')
                                    <x-menus.submenu-item
                                        content="Profile/History"
                                        selectable="0"
                                        href="{{ route('profile.edit') }}"/>
                                @endif
                                @if(Auth::user()->type == 'A')
                                <x-menus.submenu-item
                                    content="DashBoard"
                                    selectable="0"
                                    href="{{ route('statistics.show') }}"/>
                                @endif
                                @if(Auth::user()->type == 'E')
                                <x-menus.submenu-item
                                    content="Validate"
                                    selectable="0"
                                    href="{{ route('session.control') }}"/>
                                @endif
                                <x-menus.submenu-item
                                    content="Change Password"
                                    selectable="0"
                                    href="{{ route('profile.edit.password') }}"/>

                                <hr>
                                <form id="logout_form" method="POST" action="{{ route('logout') }}" class="hidden">
                                    @csrf
                                </form>
                                <x-menus.submenu-item
                                    content="Log Out"
                                    selectable="0"
                                    onclick="event.preventDefault(); document.getElementById('logout_form').submit();"
                                />

                            </x-menus.submenu>
                        @else
                            <!-- Menu Item: Login -->
                            <x-menus.menu-item
                                content="Login"
                                selectable="1"
                                href="{{ route('login') }}"
                                selected="{{ Route::currentRouteName() == 'login' }}"
                            />
                            <x-menus.menu-item
                                content="Register"
                                selectable="1"
                                href="{{ route('register') }}"
                                selected="{{ Route::currentRouteName() == 'register' }}"
                            />
                        @endauth
                    </div>

                    <!-- Hamburger -->
                    <div class="absolute right-0 top-0 flex sm:hidden pt-3 pe-3 text-black dark:text-gray-50">
                        <button id="hamburger_btn">
                            <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path id="hamburger_btn_open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path class="invisible" id="hamburger_btn_close" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white dark:bg-gray-900 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    @yield('header-title')
                </h2>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @if (session('alert-msg'))
                    <x-alert type="{{ session('alert-type') ?? 'info' }}">
                        {!! session('alert-msg') !!}
                    </x-alert>
                @endif
                @if (!$errors->isEmpty())
                    <x-alert type="warning" message="Operation failed because there are validation errors!"/>
                @endif
                @yield('main')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;
            const userTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

            const updateTheme = (theme) => {
                if (theme === 'dark' || (!theme && systemTheme)) {
                    htmlElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    htmlElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            };

            updateTheme(userTheme);

            themeToggleBtn.addEventListener('click', () => {
                if (htmlElement.classList.contains('dark')) {
                    updateTheme('light');
                } else {
                    updateTheme('dark');
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cart = document.querySelector('.x-menus-cart');
            if (cart) {
                const cartTotal = cart.getAttribute('total');
                updateCartTotal(cartTotal);
            }

            function updateCartTotal(total) {
                const cartBadge = document.querySelector('.cart-total');
                if (cartBadge) {
                    cartBadge.textContent = total;
                }
            }

            setInterval(() => {
                fetch('{{ route("cart.total") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateCartTotal(data.total);
                    });
            }, 60000);
        });
    </script>
</body>

</html>
