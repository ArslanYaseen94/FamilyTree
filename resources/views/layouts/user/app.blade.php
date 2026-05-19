<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NEXTCOME - Family Tree Creator </title>
    <link rel="stylesheet" href="{{ asset('assets/front-end/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front-end/css/feather.css') }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/front-end/images/nextcome_favicone.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/front-end/css/style.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/front-end/css/video-player.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
        }

        a {
            text-decoration: none
        }

        .offcanvas-custom {
            position: fixed;
            top: 0;
            left: -250px;
            /* Start hidden offscreen */
            width: 250px;
            height: 100%;
            background-color: #fff;
            z-index: 1050;
            transition: left 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        .offcanvas-custom.show {
            left: 0;
            /* Slide in */
        }
    </style>
    <style>
        .pricing-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
        }

        .pricing-header {
            background-color: #00a6c4;
            color: #fff;
            padding: 20px 10px;
            font-size: 24px;
            font-weight: bold;
        }

        .pricing-body {
            padding: 20px;
        }

        .pricing-title {
            font-size: 20px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 10px;
            border-bottom: 1px dotted #aaa;
            display: inline-block;
        }

        .current-plan {
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-top: 15px;
        }

        .paypal-button {
            margin-top: 20px;
            text-align: center;
        }

        .paypal-button img {
            height: 40px;
        }

        .pricing-features {
            list-style: none;
            padding-left: 0;
            margin-top: 15px;
            margin-bottom: 0;
        }

        .pricing-features li {
            margin-bottom: 10px;
        }
    </style>
    <style>
        .nav-link.active,
        .nav-content-bttn.active {
            background-color: #ffffff !important;
            color: #000 !important;
            font-weight: bold;
            border-radius: 6px;
        }

        .nav-link.active i,
        .nav-content-bttn.active i {
            color: white !important;
        }

        .navigation .nav-content ul li>a.active {
            background: linear-gradient(135deg, var(--theme-color), var(--theme-color-shade)) !important;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            color: white !important;
        }

        .nav-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            background-color: white !important;
        }

        .main-content {
            padding-top: 100px !important;
        }

        .main-wrap,
        .main-wrapper {
            z-index: 2 !important;
        }
    </style>
    <!-- Include Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
</head>

<body class="color-theme-blue mont-font" style="padding:20px">
    <div class="preloader"></div>

    <div class="main-wrap container-fluid px-0">

        <div class="nav-header bg-white shadow-xs border-0 d-flex align-items-center px-3 py-2 justify-content-between fixed-top">

            <a href="{{ route('user.dashboard') }}" class="d-flex align-items-center">
                <img src="{{ asset('assets/front-end/images/nextcome_logo.png') }}" style="width: 100px;" alt="Logo">
            </a>

            @php
            $locale = Session::get('locale', 'en');
            $language = DB::table('tbl_language')->where('LanguageCode', $locale)->value('LanguageName') ?? 'English';
            $languages = DB::table('tbl_language')->get();
            $user = Auth::guard('web')->user();
            @endphp

            <div class="d-flex align-items-center ms-auto">
                <!-- Language Dropdown -->
                <div class="dropdown me-3">
                    <a href="#" id="dropdownMenuLanguage" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="feather-globe font-xl text-current"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-3 rounded-3 shadow-lg" aria-labelledby="dropdownMenuLanguage">
                        @foreach ($languages as $lang)
                        <a href="{{ route('setLanguage', ['locale' => $lang->LanguageCode]) }}"
                            class="dropdown-item {{ $lang->LanguageCode == $locale ? 'active' : '' }}">
                            {{ $lang->LanguageName }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Profile Image -->
                <a href="{{ route('user.profile') }}" class="me-3">
                    <img src="{{ !empty($user->profileImg) ? asset('assets/front-end/ProfileImgs/' . $user->profileImg) : asset('assets/front-end/images/avtar.jpg') }}"
                        class="rounded-circle" width="40" height="40" alt="Profile Image">
                </a>

                <!-- Mobile Sidebar Toggle -->
                <button id="mobileMenuBtn" class="btn btn-link d-block d-xl-none">
                    <i class="feather-menu font-xl"></i>
                </button>
            </div>
        </div>
        <nav class="navigation scroll-bar d-none d-xl-block">
            <div class="container ps-0 pe-0">
                <div class="nav-content">
                    <div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss pt-3 pb-1 mb-2 mt-2">
                        <div class="nav-caption fw-600 font-xssss text-grey-500"><span>{{ __('messages.Account') }}</span></div>
                        <ul class="mb-1 top-content">

                            <li><a href="{{ route('user.dashboard') }}" class="nav-content-bttn open-font {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                    <i class="feather-tv btn-round-md bg-blue-gradiant me-3"></i><span>{{ __('messages.News feed') }}</span></a></li>

                            <!-- Family Listing -->
                            <li class="has-submenu">
                                <a class="nav-content-bttn open-font d-flex justify-content-between align-items-center {{ request()->routeIs('user.familytree', 'user.import', 'user.export') ? 'active' : '' }}"
                                    data-bs-toggle="collapse" href="#familylistings" role="button"
                                    aria-expanded="{{ request()->routeIs('user.familytree', 'user.import', 'user.export',"user.addmember") ? 'true' : 'false' }}"
                                    aria-controls="familylistings">
                                    <div>
                                        <i class="feather-users btn-round-md bg-primary-gradiant me-3"></i>
                                        <span>{{ __('messages.Family Listing') }}</span>
                                    </div>
                                    <i class="feather-chevron-down"></i>
                                </a>
                                <div class="collapse ps-5 {{ request()->routeIs('user.familytree', 'user.import', 'user.export',"user.addmember") ? 'show' : '' }}" id="familylistings">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="{{ route('user.familytree') }}" class="nav-link {{ request()->routeIs('user.familytree',"user.addmember") ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Members Listing') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.import') }}" class="nav-link {{ request()->routeIs('user.import') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Import Members') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.export') }}" class="nav-link {{ request()->routeIs('user.export') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Export Members') }}</a></li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Message Board -->
                            <li class="has-submenu">
                                <a class="nav-content-bttn open-font d-flex justify-content-between align-items-center {{ request()->routeIs('user.messageboard', 'user.messageto') ? 'active' : '' }}"
                                    data-bs-toggle="collapse" href="#messageBoardSubmenu" role="button"
                                    aria-expanded="{{ request()->routeIs('user.messageboard', 'user.messageto') ? 'true' : 'false' }}"
                                    aria-controls="messageBoardSubmenu">
                                    <div>
                                        <i class="feather-mail btn-round-md bg-primary-gradiant me-3"></i>
                                        <span>{{ __('messages.Message Board') }}</span>
                                    </div>
                                    <i class="feather-chevron-down"></i>
                                </a>
                                <div class="collapse ps-5 {{ request()->routeIs('user.messageboard', 'user.messageto', 'user.send.message') ? 'show' : '' }}" id="messageBoardSubmenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="{{ route('user.messageboard') }}" class="nav-link {{ request()->routeIs('user.messageboard') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Messages to All Members') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.messageto') }}" class="nav-link {{ request()->routeIs('user.messageto') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Messages to You') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.send.message') }}" class="nav-link {{ request()->routeIs('user.send.message') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Send Message') }}</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li><a href="{{ route('user.blog') }}" class="nav-content-bttn open-font {{ request()->routeIs('user.blog',"user.blog.create","blogs.edit") ? 'active' : '' }}">
                                    <i class="feather-users btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('messages.Blog') }}</span></a></li>

                            <li><a href="{{ route('user.photos') }}" class="nav-content-bttn open-font {{ request()->routeIs('user.photos') ? 'active' : '' }}">
                                    <i class="feather-users btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('messages.Photo Uploading') }}</span></a></li>

                            <li><a href="{{ route('user.deceased') }}" class="nav-content-bttn open-font {{ request()->routeIs('user.deceased', 'user.death.certificate') ? 'active' : '' }}">
                                    <i class="feather-file-text btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('messages.Death Certificates') }}</span></a></li>

                            <li><a href="{{ route('user.news') }}" class="nav-content-bttn open-font {{ request()->routeIs('user.news',"user.news.create","user.news.edit") ? 'active' : '' }}">
                                    <i class="feather-users btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('messages.News') }}</span></a></li>

                            <!-- Configuration -->
                            <li class="has-submenu">
                                <a class="nav-content-bttn open-font d-flex justify-content-between align-items-center {{ request()->routeIs('user.profile', 'user.security', 'user.templates', 'user.socialmedia', 'user.memberships') ? 'active' : '' }}"
                                    data-bs-toggle="collapse" href="#configuration" role="button"
                                    aria-expanded="{{ request()->routeIs('user.profile', 'user.security', 'user.templates', 'user.socialmedia', 'user.memberships') ? 'true' : 'false' }}"
                                    aria-controls="configuration">
                                    <div>
                                        <i class="feather-users btn-round-md bg-primary-gradiant me-3"></i>
                                        <span>{{ __('messages.Configuration') }}</span>
                                    </div>
                                    <i class="feather-chevron-down"></i>
                                </a>
                                <div class="collapse ps-5 {{ request()->routeIs('user.profile', 'user.security', 'user.templates', 'user.socialmedia', 'user.memberships') ? 'show' : '' }}" id="configuration">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.General') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.security') }}" class="nav-link {{ request()->routeIs('user.security') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Security') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.templates') }}" class="nav-link {{ request()->routeIs('user.templates') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Templates') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.socialmedia') }}" class="nav-link {{ request()->routeIs('user.socialmedia') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Social Media') }}</a></li>
                                        <li class="nav-item"><a href="{{ route('user.memberships') }}" class="nav-link {{ request()->routeIs('user.memberships') ? 'active' : '' }}" style="font-size: 12px">{{ __('messages.Membership Selection') }}</a></li>
                                    </ul>
                                </div>
                            </li>

                            <li><a href="{{ route('user.logout') }}" class="nav-content-bttn open-font">
                                    <i class="feather-log-out btn-round-md bg-primary-gradiant me-3"></i><span>{{ __('messages.Logout') }}</span></a></li>

                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div id="mobileSidebar" class="offcanvas-custom">
            <div class="offcanvas-header" style="padding: 20px;">
                <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ __('messages.Menu') }}</h5>
                <button type="button" class="btn-close" id="closeSidebar"></button>
            </div>
            <div class="offcanvas-body p-3">
                <ul class="nav flex-column">

                    {{-- News Feed --}}
                    <li>
                        <a href="{{ route('user.dashboard') }}"
                            class="nav-link {{ request()->routeIs('user.dashboard') ? 'active fw-bold text-primary' : '' }}">
                            <i class="feather-tv me-2"></i> {{ __('messages.News feed') }}
                        </a>
                    </li>

                    {{-- Family Listing --}}
                    @php
                    $familyRoutes = ['user.familytree', 'user.import', 'user.export', 'user.addmember'];
                    $isFamilyActive = request()->routeIs(...$familyRoutes);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center {{ $isFamilyActive ? 'active fw-bold text-primary' : '' }}"
                            data-bs-toggle="collapse"
                            href="#mobileFamilyMenu"
                            role="button"
                            aria-expanded="{{ $isFamilyActive ? 'true' : 'false' }}">
                            <span><i class="feather-users me-2"></i>{{ __('messages.Family Listing') }}</span>
                            <i class="feather-chevron-down"></i>
                        </a>
                        <div class="collapse ps-3 {{ $isFamilyActive ? 'show' : '' }}" id="mobileFamilyMenu">
                            <a href="{{ route('user.familytree') }}"
                                class="nav-link small {{ request()->routeIs('user.familytree', 'user.addmember') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Members Listing') }}
                            </a>
                            <a href="{{ route('user.import') }}"
                                class="nav-link small {{ request()->routeIs('user.import') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Import Members') }}
                            </a>
                            <a href="{{ route('user.export') }}"
                                class="nav-link small {{ request()->routeIs('user.export') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Export Members') }}
                            </a>
                        </div>
                    </li>

                    {{-- Message Board --}}
                    @php
                    $messageRoutes = ['user.messageboard', 'user.messageto', 'user.send.message'];
                    $isMessageActive = request()->routeIs(...$messageRoutes);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center {{ $isMessageActive ? 'active fw-bold text-primary' : '' }}"
                            data-bs-toggle="collapse"
                            href="#mobileMessageMenu"
                            role="button"
                            aria-expanded="{{ $isMessageActive ? 'true' : 'false' }}">
                            <span><i class="feather-mail me-2"></i>{{ __('messages.Message Board') }}</span>
                            <i class="feather-chevron-down"></i>
                        </a>
                        <div class="collapse ps-3 {{ $isMessageActive ? 'show' : '' }}" id="mobileMessageMenu">
                            <a href="{{ route('user.messageboard') }}"
                                class="nav-link small {{ request()->routeIs('user.messageboard') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Messages to All Members') }}
                            </a>
                            <a href="{{ route('user.messageto') }}"
                                class="nav-link small {{ request()->routeIs('user.messageto') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Messages to You') }}
                            </a>
                            <a href="{{ route('user.send.message') }}"
                                class="nav-link small {{ request()->routeIs('user.send.message') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Send Message') }}
                            </a>
                        </div>
                    </li>

                    {{-- Blog --}}
                    <li>
                        <a href="{{ route('user.blog') }}"
                            class="nav-link {{ request()->routeIs('user.blog', 'user.blog.create', 'blogs.edit') ? 'active fw-bold text-primary' : '' }}">
                            <i class="feather-file-text me-2"></i> {{ __('messages.Blog') }}
                        </a>
                    </li>

                    {{-- Photos --}}
                    <li>
                        <a href="{{ route('user.photos') }}"
                            class="nav-link {{ request()->routeIs('user.photos') ? 'active fw-bold text-primary' : '' }}">
                            <i class="feather-image me-2"></i> {{ __('messages.Photo Uploading') }}
                        </a>
                    </li>

                    {{-- Death Certificates --}}
                    <li>
                        <a href="{{ route('user.deceased') }}"
                            class="nav-link {{ request()->routeIs('user.deceased', 'user.death.certificate') ? 'active fw-bold text-primary' : '' }}">
                            <i class="feather-file-text me-2"></i> {{ __('messages.Death Certificates') }}
                        </a>
                    </li>

                    {{-- News --}}
                    <li>
                        <a href="{{ route('user.news') }}"
                            class="nav-link {{ request()->routeIs('user.news', 'user.news.create', 'user.news.edit') ? 'active fw-bold text-primary' : '' }}">
                            <i class="feather-clipboard me-2"></i> {{ __('messages.News') }}
                        </a>
                    </li>

                    {{-- Configuration --}}
                    @php
                    $configRoutes = ['user.profile', 'user.security', 'user.templates', 'user.socialmedia', 'user.memberships'];
                    $isConfigActive = request()->routeIs(...$configRoutes);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center {{ $isConfigActive ? 'active fw-bold text-primary' : '' }}"
                            data-bs-toggle="collapse"
                            href="#mobileConfigMenu"
                            role="button"
                            aria-expanded="{{ $isConfigActive ? 'true' : 'false' }}">
                            <span><i class="feather-settings me-2"></i>{{ __('messages.Configuration') }}</span>
                            <i class="feather-chevron-down"></i>
                        </a>
                        <div class="collapse ps-3 {{ $isConfigActive ? 'show' : '' }}" id="mobileConfigMenu">
                            <a href="{{ route('user.profile') }}"
                                class="nav-link small {{ request()->routeIs('user.profile') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.General') }}
                            </a>
                            <a href="{{ route('user.security') }}"
                                class="nav-link small {{ request()->routeIs('user.security') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Security') }}
                            </a>
                            <a href="{{ route('user.templates') }}"
                                class="nav-link small {{ request()->routeIs('user.templates') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Templates') }}
                            </a>
                            <a href="{{ route('user.socialmedia') }}"
                                class="nav-link small {{ request()->routeIs('user.socialmedia') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Social Media') }}
                            </a>
                            <a href="{{ route('user.memberships') }}"
                                class="nav-link small {{ request()->routeIs('user.memberships') ? 'active fw-bold text-primary' : '' }}">
                                {{ __('messages.Membership Selection') }}
                            </a>
                        </div>
                    </li>

                    {{-- Logout --}}
                    <li>
                        <a href="{{ route('user.logout') }}" class="nav-link text-danger">
                            <i class="feather-log-out me-2"></i> {{ __('messages.Logout') }}
                        </a>
                    </li>

                </ul>
            </div>
        </div>

    </div>

    @yield('content')

    </div>
    <script src="{{ asset('assets/front-end/js/plugin.js') }}"></script>
    <script src="{{ asset('assets/front-end/js/scripts.js') }}"></script>

    <script src="{{ asset('assets/front-end/js/video-player.js') }}"></script>
    @yield('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('mobileSidebar');
            const closeBtn = document.getElementById('closeSidebar');

            // Toggle sidebar on menu button click
            menuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });

            // Close sidebar on close button click
            closeBtn.addEventListener('click', function() {
                sidebar.classList.remove('show');
            });

            // Close sidebar on outside click
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMenuBtn = menuBtn.contains(event.target);
                if (!isClickInsideSidebar && !isClickOnMenuBtn) {
                    sidebar.classList.remove('show');
                }
            });

            // Close sidebar on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    sidebar.classList.remove('show');
                }
            });

            // 🧠 NEW: Hide sidebar on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>



    @if(session(__('messages.success')))
    <script>
        toastr.success("{{ session(__('messages.success')) }}", '{{ __(__("messages.success")) }}', {
            timeOut: 5000
        });
    </script>
    @endif

    @if(session(__('messages.error')))
    <script>
        toastr.error("{{ session(__('messages.error')) }}", '{{ __(__("messages.error")) }}', {
            timeOut: 5000
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        @foreach($errors->all() as $error)
            toastr.error("{{ $error }}", '{{ __("messages.Error") }}', { timeOut: 5000 });
        @endforeach
    </script>
    @endif

    @include('user-view.chatbot.chat')
</body>

</html>