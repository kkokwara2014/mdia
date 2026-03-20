
<!doctype html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no" />
    <title>@yield('title')</title>
    {{-- <title>Mbaise Diaspora in the Americas (MDIA) | Igbo Community in USA & Americas</title> --}}

<meta name="description" content="Mbaise Diaspora in the Americas (MDIA) is a vibrant Igbo community organization uniting Mbaise people across the Americas. We promote culture, networking, development, and community support. Join us today.">

<meta name="keywords" content="Mbaise Diaspora, MDIA, Igbo community USA, Mbaise USA, Nigerian diaspora Americas, Igbo association abroad, Mbaise organization, Nigerian community USA, cultural association, diaspora networking">

<meta name="author" content="Mbaise Diaspora in the Americas">

<meta property="og:title" content="Mbaise Diaspora in the Americas (MDIA)">
<meta property="og:description" content="Connecting Mbaise people across the Americas for culture, unity, and development.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://mbaisedia.org/">
<meta property="og:image" content="https://mbaisedia.org/logo.png">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Mbaise Diaspora in the Americas (MDIA)">
<meta name="twitter:description" content="Join the Mbaise community in the Americas. Connect, grow, and support one another.">
    <!-- Favicon -->    
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('frontend/assets/images/fav.png')}}">
    <!-- Animate CSSS -->    
    <link href="{{asset('frontend/assets/library/animate/animate.min.css')}}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="{{asset('frontend/assets/library/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Icofont CSS -->
    <link href="{{asset('frontend/assets/library/icofont/icofont.min.css')}}" rel="stylesheet">
    <!-- Owl Carousel CSS -->
    <link href="{{asset('frontend/assets/library/owlcarousel/css/owl.carousel.min.css')}}" rel="stylesheet">
    <!-- Select Dropdown CSS -->
    <link href="{{asset('frontend/assets/library/select2/css/select2.min.css')}}" rel="stylesheet">
    <!-- Magnific Popup CSS -->
    <link href="{{asset('frontend/assets/library/magnific-popup/magnific-popup.css')}}" rel="stylesheet">    
    <!-- Main Theme CSS -->
    <link href="{{asset('frontend/assets/css/style.css')}}" rel="stylesheet">	
</head>
<body>

    <!-- Page loader Start 
    <div id="pageloader">   
        <div class="loader-item">
            <div class="loader">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
              </div>
        </div>
    </div>
     Page loader End -->

    <!-- Header Start -->
    <header class="homestyle-third">
        <!-- Main Navigation Start -->
        <nav class="navbar navbar-expand-lg header-fullpage">
            <div class="container text-nowrap">
                <div class="d-flex align-items-center w-100 col p-0 logo-brand">
                    <a class="navbar-brand rounded-bottom light-bg" href="{{route('index')}}">
                        <img src="{{asset('frontend/assets/images/logo__.png')}}" alt="" style="min-width: 250px; max-width: 250px;">
                    </a> 
                </div>
                <!-- Topbar Buttons Start -->
                <div class="d-inline-flex request-btn order-lg-last col-auto p-0 align-items-center"> 
                    <a class="nav-link btn btn-primary ms-3 donate-btn" href="{{route('login')}}">LOGIN</a>

                    <!-- Toggle Button Start -->
                    <button class="navbar-toggler x collapsed" type="button" data-bs-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Toggle Button End -->  
                </div>
                <!-- Topbar Buttons End -->

                <div class="navbar-collapse">
                    <!-- Mobile Logo -->
                    <div class="offcanvas-header">
                        <a href="{{route('index')}}" class="logo-small">
                            <img src="{{asset('frontend/assets/images/logo__.png')}}" alt="">
                        </a>                        
                    </div>
                    <!-- Mobile Logo -->
                    <!-- Mobile Menu -->
                    <div class="offcanvas-body">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('index')}}">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="{{route('index')}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">About </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('about')}}#vison-mission">Mission</a></li>
                                    <li><a class="dropdown-item" href="{{route('about')}}#vison-mission">Vision</a></li>
                                    <li><a class="dropdown-item" href="{{route('about')}}#leaders">Our Leaders</a></li>                
                                </ul>
                            </li>
                            <li class="nav-item d-lg-none mt-2">
                                <a class="btn btn-primary w-100 text-white text-center py-2 rounded" href="{{ route('login') }}">LOGIN</a>
                            </li>
                        </ul>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="close-nav"></div>
                    <!-- Main Navigation End -->
                </div>
            </div>
        </nav>
        <!-- Main Navigation End -->
    </header>
    <!-- Header Start -->