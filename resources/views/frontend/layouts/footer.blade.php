<footer class="wide-tb-70 footer-style-3">
        <div class="container pos-rel">
            <div class="row align-items-end top-row">
                <div class="col-lg-4 order-lg-last">
                    <div class="footer-subscribe-white">
                        <h2><i data-feather="send"></i> Newsletter Subscribe</h2>
                        <div class="input-wrap">
                            <div class="mb-4">
                                <input type="text" name="email" placeholder="Enter Your Email" readonly class="form-control">
                            </div> 
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-outline-primary btn-block">Subscribe now</button>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="col-lg-8">
                    <div class="d-md-flex align-items-end justify-content-between">
                        <div class="logo-footer">
                            <a href="{{route('index')}}">
                                <img src="{{asset('frontend/assets/images/logo__2.png')}}" alt="">
                            </a>
                        </div>

                        <div class="social-icons">
                            <ul class="list-unstyled list-group list-group-horizontal">
                                <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                <li><a href="#"><i class="icofont-instagram"></i></a></li>
                                <li><a href="#"><i class="icofont-youtube-play"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="row">
                <!-- Column First -->
                <div class="col-lg-6 col-md-6">                    
                    <h3 class="footer-heading">About Us</h3>

                    <p>We serve as a platform for unity, cultural preservation, mutual support, and development initiatives that benefit both our members and our communities back home in Mbaise.</p>  
                </div>
                <!-- Column First -->

                <!-- Spacer For Medium -->
                <div class="w-100 d-none d-md-block d-lg-none spacer-30"></div>
                <!-- Spacer For Medium -->

                <!-- Column Second -->
                <div class="col-lg-3 col-md-6">
                    <h3 class="footer-heading">Explore Us</h3>
                    <div class="footer-widget-menu">
                        <ul class="list-unstyled">
                            <li><a href="{{route('about')}}"><i class="icofont-simple-right"></i> <span>About Us</span></a></li>
                            <li><a href="{{route('about')}}#vison-mission"><i class="icofont-simple-right"></i> <span>Mission</span></a></li>
                            <li><a href="{{route('about')}}#vison-mission"><i class="icofont-simple-right"></i> <span>Vision</span></a></li>
                            <li><a href="{{route('about')}}#leaders"><i class="icofont-simple-right"></i> <span>Our Leaders</span></a></li>
                        </ul>
                    </div>
                </div>
                <!-- Column Second -->
            </div>
        </div>  

        <div class="copyright-wrap">
            <div class="container pos-rel">
                <div class="row text-md-start text-center">
                    <div class="col-sm-12 col-md-auto copyright-text">
                        © Copyright <span class="txt-blue">Mbaise Diaspora</span> <span id="yearText"></span>.   |   Created by <a href="https://donerightsystems.org/" target="_blank">Done-Right Systems</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Main Footer End -->



    <!-- Back To Top Start -->
    <a id="mkdf-back-to-top" href="#" class="off"><i data-feather="corner-right-up"></i></a>
    <!-- Back To Top End -->

    <!-- Jquery Library JS -->
    <script src="{{asset('frontend/assets/library/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap JS -->
    <script src="{{asset('frontend/assets/library/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Feather Icon JS -->
    <script src="{{asset('frontend/assets/library/feather-icons/feather.min.js')}}"></script>
    <!-- Owl Carousel JS -->
    <script src="{{asset('frontend/assets/library/owlcarousel/js/owl.carousel.min.js')}}"></script>
    <!-- Select2 Dropdown JS -->
    <script src="{{asset('frontend/assets/library/select2/js/select2.min.js')}}"></script>
    <!-- Magnific Popup JS -->
    <script src="{{asset('frontend/assets/library/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <!-- jflickrfeed Images JS -->
    <script src="{{asset('frontend/assets/library/jflickrfeed/jflickrfeed.min.js')}}"></script>
    <!-- Way Points JS -->
    <script src="{{asset('frontend/assets/library/jquery-waypoints/jquery.waypoints.min.js')}}"></script>
    <!-- Count Down JS -->
    <script src="{{asset('frontend/assets/library/countdown/jquery.countdown.min.js')}}"></script>
    <!-- Appear JS -->
    <script src="{{asset('frontend/assets/library/jquery-appear/jquery.appear.js')}}"></script>
    <!-- Jquery Easing JS -->
    <script src="{{asset('frontend/assets/library/jquery-easing/jquery.easing.min.js')}}"></script>
    <!-- Counter JS -->
    <script src="{{asset('frontend/assets/library/jquery.counterup/jquery.counterup.min.js')}}"></script>
    <!-- Form Validation JS -->
    <script src="{{asset('frontend/assets/library/jquery-validate/jquery.validate.min.js')}}"></script>
    <!-- Theme Custom -->
    <script src="{{asset('frontend/assets/js/site-custom.js')}}"></script>
</body>
</html>