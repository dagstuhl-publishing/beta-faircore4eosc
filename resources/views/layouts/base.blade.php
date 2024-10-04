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

            <a class="navbar-brand" href="https://www.dagstuhl.de/">
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
                    @endauth
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <a class="navbar-brand" href="https://faircore4eosc.eu/">
                        <img style="height: 1.5em" src="{{ asset("images/fairLogo.svg") }}" alt="FAIRCORE4EOSC">
                    </a>
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

    <main style="padding: 5rem 0rem">
        <div class="container">
            @if (session("success") !== null)
                <div class="alert alert-success alert-dismissible">
                    {{ session("success") }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session("warning") !== null)
                <div class="alert alert-warning alert-dismissible">
                    {{ session("warning") }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible">
                        {{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
        </div>

        @yield("body")
    </main>

    <footer class="fixed-bottom d-flex align-items-center justify-content-between p-2 border-top bg-light">
        <div class="text-secondary d-flex align-items-center gap-3">
            <a href="https://www.dagstuhl.de/en/publishing"><img style="height: 2em" src="{{ asset("images/pubLogo-yellow.svg") }}" alt="Dagstuhl Publishing"></a>
            <span>
                &copy; 2024
                <a href="https://www.dagstuhl.de/" class="text-secondary text-decoration-none">Schloss Dagstuhl &ndash; LZI GmbH</a>
            </span>
        </div>
        <ul class="nav col-md-4 justify-content-end">
            <li class="nav-item">
                <a href="https://github.com/dagstuhl-publishing/beta-faircore4eosc" class="nav-link px-2 text-secondary">
                    GitHub
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link px-2 text-body-secondary">
                    Imprint
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link px-2 text-body-secondary">
                    Privacy
                </a>
            </li>
        </ul>
    </footer>
</body>
</html>
