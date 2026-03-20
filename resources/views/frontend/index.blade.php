
@extends('frontend.layouts.app')

@section('content')


@section('title')
Mbaise Diaspora - Home
@endsection

    <!-- Banner Start -->
    <section class="static-banner">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-lg-5">
                    <div class="banner-text">
                        <h3>
                            Uniting Mbaise Across the Americas
                        </h3>
                        <p>Connecting sons and daughters of Mbaise living across North, Central, and South America to preserve our heritage, strengthen our community, and support the development of our homeland.</p>
                        <div class="hstack">                                        
                            <a class="btn btn-primary me-3" href="about.html" role="button">Learn More</a>
                            
                        </div>            
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="banner-img">
                        <img src="{{asset('frontend/assets/images/video_img.jpg')}}" alt="">
                        <div class="funds-committed">
                            <small>Total Members</small>
                            <span class="counter">240</span>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </section>
    <!-- Banner Start -->

    <!-- Main Body Content Start -->
    <main id="body-content">



        <!-- About Us Style Start -->
        <section class="wide-tb-100 bg-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 col-md-12">
                        <div class="text-center">
                            <img src="{{asset('frontend/assets/images/about_img.png')}}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <h1 class="heading-main">
                            <small>About Us</small>
                            Welcome to Mbaise Diaspora in the Americas
                        </h1>

                        <p>Mbaise Diaspora in the Americas (MDIA) is a vibrant association of sons and daughters of Mbaise living across North, Central, and South America. Our organization brings together individuals who share a common heritage, culture, and commitment to the progress of our homeland and community abroad.</p>
                        <p>We serve as a platform for unity, cultural preservation, mutual support, and development initiatives that benefit both our members and our communities back home in Mbaise.</p>
                        <p>Through collaboration, networking, and shared purpose, we strive to strengthen the bond among Mbaise people across the Americas while contributing meaningfully to the growth and well-being of our homeland.</p>

                        <div class="icon-box-1 my-4">
                            <i class="charity-volunteer_people"></i>
                            <div class="text">
                                <h3>What We Do</h3>
                                <p>
                                    <ul>
                                        <li>Promote unity among Mbaise people living in the diaspora</li>
                                        <li>Preserve and celebrate Mbaise culture and traditions</li>
                                        <li>Support community development initiatives in Mbaise</li>
                                        <li>Provide a network of support and collaboration for members</li>
                                        <li>Encourage youth engagement and leadership</li>
                                    </ul>
                                </p>
                            </div>
                        </div>    
                        
                        <div class="d-flex">
                            <a class="btn btn-default mr-3" href="about.html">Learn More</a>
                        </div>


                    </div>
                </div>
            </div>
        </section>
        <!-- About Us Style Start -->
        
           
    </main>

    <!-- Main Footer Start -->
@endsection