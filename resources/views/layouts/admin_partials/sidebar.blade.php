<div>
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <!-- Sidebar -->
    <div id="sidebar"
        class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:!w-64 shrink-0 bg-slate-800 p-4 transition-all duration-200 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false" x-cloak="lg">

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-1">
            <!-- Close button -->
            <button class="lg:hidden text-slate-500 hover:text-slate-400" @click.stop="sidebarOpen = !sidebarOpen"
                aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <!-- Logo -->
            <div class="hidden lg:sidebar-expanded:block text-xl pt-1 text-white">Dashboard</div>
            <a class="block" href="#">

                <svg class="w-[40px] h-[40px] text-white" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <circle cx="12" cy="13" r="2" />
                    <line x1="13.45" y1="11.55" x2="15.5" y2="9.5" />
                    <path d="M6.4 20a9 9 0 1 1 11.2 0Z" />
                </svg>
            </a>
        </div>

        <!-- Links -->
        <div class="space-y-8">
            <!-- Pages group -->
            <div>
                <h3 class="text-xs uppercase text-slate-500 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
                        aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Pages</span>
                </h3>
                <ul class="mt-3">

                    <!-- Home/Statistics -->
                    @can('viewAny', App\Models\User::class)
                        @php
                            $options = [];
                            $options['statistics'] = route('statistics.show');
                        @endphp
                        <x-menus.admin-group-menu-items title="Home" :options="$options">
                            <svg class="w-6 h-6 text-gray-400 dark:text-white" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Users -->
                    @can('viewAny', App\Models\User::class)
                        @php
                            $options = [];
                            $options['Users'] = route('users.index');
                        @endphp
                        <x-menus.admin-group-menu-items title="Users" :options="$options">
                            <svg class="w-6 h-6 text-gray-400 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H6Zm7.25-2.095c.478-.86.75-1.85.75-2.905a5.973 5.973 0 0 0-.75-2.906 4 4 0 1 1 0 5.811ZM15.466 20c.34-.588.535-1.271.535-2v-1a5.978 5.978 0 0 0-1.528-4H18a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-4.535Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Movies -->
                    @can('viewAny', App\Models\Movie::class)
                        @php
                            $options = [];
                            $options['Movies'] = route('movies.index');
                        @endphp
                        <x-menus.admin-group-menu-items title="Movies" :options="$options">
                            <svg class="text-gray-400 dark:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M11.266 7l12.734-2.625-.008-.042-1.008-4.333-21.169 4.196c-1.054.209-1.815 1.134-1.815 2.207v14.597c0 1.657 1.343 3 3 3h18c1.657 0 3-1.343 3-3v-14h-12.734zm8.844-5.243l2.396 1.604-2.994.595-2.398-1.605 2.996-.594zm-5.898 1.169l2.4 1.606-2.994.595-2.401-1.607 2.995-.594zm-5.904 1.171l2.403 1.608-2.993.595-2.406-1.61 2.996-.593zm-2.555 5.903l2.039-2h3.054l-2.039 2h-3.054zm4.247 10v-7l6 3.414-6 3.586zm4.827-10h-3.054l2.039-2h3.054l-2.039 2zm6.012 0h-3.054l2.039-2h3.054l-2.039 2z" fill="currentColor"/>
                            </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Theaters -->
                    @can('viewAny', App\Models\Theater::class)
                        @php
                            $options = [];
                            $options['Theaters'] = route('theaters.index');
                        @endphp
                        <x-menus.admin-group-menu-items title="Theaters" :options="$options">
                        <svg class="text-gray-400 dark:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 3H3v14H21V3z" />
                            <path d="M3 17h18v4H3z" />
                            <circle cx="7" cy="19" r="1" />
                            <circle cx="12" cy="19" r="1" />
                            <circle cx="17" cy="19" r="1" />
                        </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Genres -->
                    @can('viewAny', App\Models\Genre::class)
                        @php
                            $options = [];
                            $options['Genres'] = route('genres.index');
                        @endphp
                        <x-menus.admin-group-menu-items title="Genres" :options="$options">
                        <svg class="w-6 h-6 text-gray-400 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M22 4.5V8h-3V5l-2 2.5V8h-3V5l-2 2.5V8H9V5L7 7.5V8H4V5L2 7.5V8H1V4.5c0-1.1.9-2 2-2h18c1.1 0 2 .9 2 2zM2 18V10h1v3l2-2.5V10h3v3l2-2.5V10h3v3l2-2.5V10h3v3l2-2.5V10h1v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2z"/>
                        </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Screenings-->
                    @can('viewAny', App\Models\Screening::class)
                        @php
                            $options = [];
                            $options['Screening'] = route('screenings.index');
                        @endphp

                        <x-menus.admin-group-menu-items title="Screening" :options="$options">
                        <svg class="w-6 h-6 text-gray-400 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                        </svg>

                        </x-menus.admin-group-menu-items>
                    @endcan

                    <!-- Purchases -->
                    @can('viewAny', App\Models\Purchase::class)
                        @php
                            $options = [];
                            $options['Purchases'] = route('purchases.index');
                        @endphp
                        <x-menus.admin-group-menu-items title="Purchases" :options="$options">
                        <svg class="w-6 h-6 text-gray-400 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 5a2 2 0 0 0-2 2v2.5a1 1 0 0 0 1 1 1.5 1.5 0 1 1 0 3 1 1 0 0 0-1 1V17a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2.5a1 1 0 0 0-1-1 1.5 1.5 0 1 1 0-3 1 1 0 0 0 1-1V7a2 2 0 0 0-2-2H4Z"/>
                          </svg>
                        </x-menus.admin-group-menu-items>
                    @endcan


                    <!-- Only one option -->
                    <x-menus.admin-group-menu-items class="mt-2" title="Return to website" :options="['Home' => route('movies.high')]">
                        <svg class="w-6 h-6 text-gray-400 dark:text-white" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <polyline points="5 12 3 12 12 3 21 12 19 12" />
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                        </svg>
                    </x-menus.admin-group-menu-items>

                </ul>
            </div>
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="px-3 py-2">
                <button @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="w-6 h-6 fill-current sidebar-expanded:rotate-180" viewBox="0 0 24 24">
                        <path class="text-slate-400"
                            d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z" />
                        <path class="text-slate-600" d="M3 23H1V1h2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
