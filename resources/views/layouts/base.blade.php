<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config("app.name", "Laravel") }}</title>

    <!-- Scripts -->
    @vite(["resources/sass/app.scss", "resources/js/app.js"])

    @stack("scripts")
</head>

<body>
    <nav class="navbar main fixed-top navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">

            <a class="navbar-brand" href="/">
                <img class="lzi-logo" style="height: 2rem" src="{{ asset("images/LZI-Logo.jpg") }}" alt="Schloss Dagstuhl - LZI - Logo" />
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("home") }}">
                            <i class="bi bi-house large-icon"></i> Home
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route("swh-archives.index") }}">
                                <i class="bi bi-archive-fill"></i> Archives
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route("swh-deposits.index") }}">
                                <i class="bi bi-file-earmark-zip-fill"></i> Deposits
                            </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route("login") }}">{{ __("Login") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route("register") }}">{{ __("Register") }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li class="nav-item">
                                    <form method="POST" action="{{ route("logout") }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="bi bi-box-arrow-right large-icon"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @yield("body")
</body>
</html>
