<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Acsess Health Customer Centre') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ URL::asset('favicon.ico') }}"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('favicon.ico') }}"/>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ URL::asset('acsess_health.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ URL::asset('acsess_health.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ URL::asset('acsess_health.png') }}" />

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!--<script src="{{ asset('js/telephoneissue.js')}}"></script>//-->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
    <!--<v-app>//-->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                  <img src="https://acsess.com.au/wp-content/uploads/2017/04/logo-2.png" width="120" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @else
                            @if (Route::has('register') && Auth::id() == 1)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ URL::route('questionnaire') }}">Questionnaire</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ URL::route('index') }}">Submissions</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div id="dropdown-menu" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); clearLocalStorage();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
          @yield('content')

        </main>

        <footer>
            <div class="container">
              <div class="footerRight">
                &copy; Acsess Health {{ date('Y') }}
              </div>
            </div>
        </footer>

    </div>
    <input type="hidden" id="download_submissions_url" value="{{ URL::route('download-submissions') }}"/>
    <script>
        function clearLocalStorage() {
            localStorage.removeItem('ah_telephoneissue' + '.state_questionnaire_id')
            localStorage.removeItem('ah_telephoneissue' + '.state_questionnaire_questions')
            localStorage.removeItem('ah_telephoneissue' + '.state_questionnaire_log')
        }
    </script>
    <!--</v-app>//-->
</body>
</html>
