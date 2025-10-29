<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <title>{{ getPageMeta('title') }} | {{ systemSettings('site_title') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ storagelink(config('settings.site_favicon')) }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">

    <link href="{{ asset('toastr/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('sweetalert/sweetalert.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .accountbg .overlay{
            width: 100%;
            height: 100%;
            background: #0c0c0c78;
        }
    </style>
</head>

<body class="account-pages">
    <!-- Begin page -->
    <div class="accountbg"
        style="background: url('{{ asset('images/default/bg.jpg') }}');background-size: cover;background-position: center;">
        <div class="overlay"></div>
    </div>

    <div class="wrapper-page account-page-full">

        <div class="card shadow-none">
            <div class="card-block">

                <div class="account-box">

                    <div class="card-box shadow-none p-4">
                        <div class="p-2">
                            <div class="text-center mt-4">
                                <a href="index.html"><img src="{{ getStorageImage(config('settings.site_logo'),false,'logo') }}"
                                        height="40" alt="logo"></a>
                            </div>

                            <h4 class="font-size-18 mt-5 text-center">Welcome Back !</h4>
                            <p class="text-muted text-center">Sign in to continue to {{ config('settings.site_title') ?? 'Grozaar' }}.</p>

                            <form class="mt-4" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="email" placeholder="Enter email" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="userpassword">Password</label>
                                    <input type="password" name="password" required class="form-control" id="userpassword" placeholder="Enter password">
                                </div>

                                <div class="mb-3 row">
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="remember" class="form-check-input" id="customControlInline">
                                            <label class="form-check-label" for="customControlInline">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        <button class="btn btn-success w-md waves-effect waves-light" type="submit">Log In</button>
                                    </div>
                                </div>

                                {{-- <div class="mb-3 mt-2 mb-0 row">
                                    <div class="col-12 mt-3">
                                        <a href="pages-recoverpw-2.html"><i class="mdi mdi-lock"></i> Forgot your
                                            password?</a>
                                    </div>
                                </div> --}}

                            </form>

                            <div class="mt-5 pt-4 text-center">
                                {{-- <p>Don't have an account ? <a href="pages-register-2.html"
                                        class="fw-medium text-primary"> Signup now </a> </p> --}}
                                <p class="mb-0">© {{ date('Y') }}
                                    {{ config('settings.site_title') }} <span class="d-none d-sm-inline-block"> - Design & Developed By
                                        <a href="https://itclanbd.com" target="_blank">ITclan BD </a></p>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>



    <!-- JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert.min.js') }}"></script>
    <script>
        toastr.options =
            {
                "closeButton": true,
                "progressBar": true
            }
        @if(Session::has('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if(Session::has('info'))
            toastr.info("{{ session('info') }}");
        @endif
        @if(Session::has('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif


    </script>


    {{-- <script src="{{asset('assets/js/app.js')}}"></script> --}}

</body>

</html>
