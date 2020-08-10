<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>teaching1to1english</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="all,follow">
        <!-- Bootstrap CSS-->
        <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/fancybox.css') }}">
        
        <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.css') }}">

        <link rel="stylesheet" href="{{ asset('backend/fonts/web-icons/web-icons.min599c.css?v4.0.2') }}">
        <link rel="stylesheet" href="{{ asset('backend/vendor/toastr/toastr.min599c.css?v4.0.2') }}">

        @yield('head')
    </head>
    <body>
    <div class="se-pre-con"></div>
    <!-- Header -->

    <nav class="navbar navbar-default fixed-top">
        <div class="row" style="flex-grow: 1;">
            <div class="col-6 col-sm-4 col-md-2 col-lg-2 col-xl-2" id="logo">
                <i class="fa fa-bars d-inline-block d-md-none mobile-nav"></i>
                <a href="@if(Auth::check()){{ route('home') }}@else{{ route('welcome') }}@endif" class="float-xl-right"><img src="{{ asset('frontend/img/logo.png') }}" width="100" height="23" /></a>
            </div>

            <div class="col-md-2 col-lg-3 col-xl-3 d-none d-md-block">
            </div>

            <div class="col-md-2 col-lg-3 col-xl-3 d-none d-md-block">
            </div>

            <div class="col-sm-5 col-md-3 col-lg-2 col-xl-2 d-none d-sm-block">
                @if(Auth::check() && !Auth::user()->hasRole('instructor') && !Auth::user()->hasRole('admin'))
                <span class="become-instructor" href="{{ route('login') }}" data-toggle="modal" data-target="#myModal">Become Teacher</span>
                @endif
            </div>

            @guest
            <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2">
                <a class="btn btn-learna" href="{{ route('login') }}">Login / Sign Up</a>
            </div>
            @else
            <div class="col-6 col-sm-3 col-md-2 col-lg-2 col-xl-2">
                <div class="dropdown float-xl-left float-sm-right float-right" style="margin-right: 10px;">
                    <span id="notificationMenuButtonRight" data-toggle="dropdown">
                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="bell" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-bell fa-w-14 fa-fw fa-2x" style="width: 25px;"><path fill="currentColor" d="M224 480c-17.66 0-32-14.38-32-32.03h-32c0 35.31 28.72 64.03 64 64.03s64-28.72 64-64.03h-32c0 17.65-14.34 32.03-32 32.03zm209.38-145.19c-27.96-26.62-49.34-54.48-49.34-148.91 0-79.59-63.39-144.5-144.04-152.35V16c0-8.84-7.16-16-16-16s-16 7.16-16 16v17.56C127.35 41.41 63.96 106.31 63.96 185.9c0 94.42-21.39 122.29-49.35 148.91-13.97 13.3-18.38 33.41-11.25 51.23C10.64 404.24 28.16 416 48 416h352c19.84 0 37.36-11.77 44.64-29.97 7.13-17.82 2.71-37.92-11.26-51.22zM400 384H48c-14.23 0-21.34-16.47-11.32-26.01 34.86-33.19 59.28-70.34 59.28-172.08C95.96 118.53 153.23 64 224 64c70.76 0 128.04 54.52 128.04 121.9 0 101.35 24.21 138.7 59.28 172.08C421.38 367.57 414.17 384 400 384z" class=""></path></svg>
                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="bell-on" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-bell-on fa-w-20 fa-fw fa-2x" style="width: 25px; color: #f72f2f;"><path fill="currentColor" d="M528,64a16.17,16.17,0,0,0,7.16-1.69l64-32A16,16,0,0,0,584.84,1.69l-64,32A16,16,0,0,0,528,64ZM80,160H16a16,16,0,0,0,0,32H80a16,16,0,0,0,0-32ZM40.84,30.31l64,32A16.17,16.17,0,0,0,112,64a16,16,0,0,0,7.16-30.31l-64-32A16,16,0,0,0,40.84,30.31ZM624,160H560a16,16,0,0,0,0,32h64a16,16,0,0,0,0-32ZM320,480a32,32,0,0,1-32-32H256a64,64,0,1,0,128,0H352A32,32,0,0,1,320,480ZM480,185.91c0-79.6-63.37-144.5-144-152.36V16a16,16,0,0,0-32,0V33.56c-80.66,7.85-144,72.75-144,152.35,0,94.4-21.41,122.28-49.35,148.9a46.45,46.45,0,0,0-11.24,51.24A47.67,47.67,0,0,0,144,416H496a47.66,47.66,0,0,0,44.62-30,46.49,46.49,0,0,0-11.24-51.22C501.41,308.19,480,280.33,480,185.91ZM496,384H144c-14.22,0-21.34-16.47-11.31-26C167.53,324.8,192,287.66,192,185.91,192,118.53,249.22,64,320,64s128,54.52,128,121.91c0,101.34,24.22,138.68,59.28,172.07C517.38,367.56,510.16,384,496,384Z" class="d-none"></path></svg>
                    </span>
                      
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationMenuButtonRight">

                      <a class="dropdown-item" href="javascript:void(0)" >
                          <span id="urgentLessons"></span> urgent lessons exist
                      </a>
                      
                    </div>
                </div>
                <div class="dropdown float-xl-left float-sm-right float-right">
                    <span id="dropdownMenuButtonRight" data-toggle="dropdown">{{ Auth::user()->first_name }} &nbsp;<i class="fa fa-caret-down"></i></span>
                    
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButtonRight">
                    
                    @if(Auth::user()->hasRole('instructor'))
                    <a class="dropdown-item" href="{{ route('instructor.dashboard') }}" >
                        <i class="fa fa-sign-out-alt"></i> Instructor
                    </a>
                    @endif

                    <button type="submit" class="dropdown-item" form="goMyCoursesForm">
                        <i class="fa fa-sign-out-alt"></i> My Lessons
                    </button>

                    <a class="dropdown-item" href="{{ route('logOut') }}" >
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                    
                  </div>
                </div>
                <form action="{{ route('my.courses') }}" id="goMyCoursesForm">
                    <input type="hidden" name="tzname">
                </form>
            </div>
            @endguest
        </div>
    </nav>

    @php 
        $categories = SiteHelpers::active_categories();
    @endphp
    
    <div id="sidebar">
        <ul>
           <li><a href="javascript:void(0)" class="sidebar-title">Categories</a></li>
           @foreach ($categories as $category)
           <li>
                <a href="{{ $category->slug }}">
                    <i class="fa {{ $category->icon_class }} category-menu-icon"></i>
                    {{ $category->name}}
                </a>
           </li>
           @endforeach
        </ul>
    </div>
    @yield('content')

    <!-- footer start -->
    <footer id="main-footer">
        <div class="row m-0">
            <div class="col-lg-2 col-md-4 col-sm-4 col-6 mt-3">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 col-6 mt-3">
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 mt-3 d-none d-sm-block">
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 text-center mt-4">
                <img src="{{ asset('frontend/img/logo_footer.png') }}" class="img-fluid" width="210" height="48">
                <br>
                <span id="c-copyright">
                    Copyright © 2020, teaching1to1english. All rights reserved.
                </span>
            </div>
        </div>
    </footer>
    <!-- footer end -->

    <!-- The Modal start -->
    <div class="modal" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bi-header ">
            <h5 class="col-12 modal-title text-center bi-header-seperator-head">Become an Instructor</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
           
        <div class="becomeInstructorForm">
           <form id="becomeInstructorForm" class="form-horizontal" method="POST" action="{{ route('become.instructor') }}">
            {{ csrf_field() }}
                <div class="px-4 py-2">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label>First Name</label>
                                <input type="text" class="form-control form-control-sm" placeholder="First Name" name="first_name">
                            </div>
                            <div class="col-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Last Name" name="last_name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Contact Email" name="contact_email">
                    </div>

                    <div class="form-group">
                        <label>Telephone</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Telephone" name="telephone">
                    </div>

                    <div class="form-group">
                        <label>Paypal ID</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Paypal ID" name="paypal_id">
                    </div>

                    <div class="form-group">
                        <label>Biography</label>
                        <textarea class="form-control form-control" placeholder="Biography" name="biography"></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-lg btn-block login-page-button">Submit</button>
                    </div>

                </div>
                </form>
            </div>
        </div>
      </div>
    </div>
    <!-- The Modal end -->
    </body>
    <script src="{{ asset('frontend/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/js/fancybox.min.js') }}"></script>
    <script src="{{ asset('frontend/js/modernizr.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.validate.js') }}"></script>
    
    <!-- Toastr -->
    <script src="{{ asset('backend/vendor/toastr/toastr.min599c.js?v4.0.2') }}"></script>

    <script src="{{ asset('backend/vendor/moment/moment.min599c.js') }}"></script>
    <script src="{{ asset('backend/vendor/moment/moment-timezone-with-data.min.js') }}"></script>

    <script>
    $(window).on("load", function (e){
        // Animate loader off screen
        $(".se-pre-con").fadeOut("slow");
    });
    </script>
    <script type="text/javascript">
        $(document).ready(function()
        {
            $("input[name='tzname']").val(moment.tz.guess());

            /* Delete record */
            $('.delete-record').click(function(event)
            {
                var url = $(this).attr('href');
                event.preventDefault();
                
                if(confirm('Are you sure want to delete this record?'))
                {
                    window.location.href = url;
                } else {
                    return false;
                }

            });

            /* Toastr messages */
            toastr.options.closeButton = true;
            toastr.options.timeOut = 5000;
            @if(session()->has('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if(session()->has('status'))
                toastr.success("{{ session('status') }}");
            @endif
            @if(session()->has('error'))
                toastr.error("{{ session('error') }}");
            @endif
            @if(session()->has('info'))
                toastr.info("{{ session('info') }}");
            @endif

            $('.mobile-nav').click(function()
            {
                $('#sidebar').toggleClass('active');
                
                $(this).toggleClass('fa-bars');
                $(this).toggleClass('fa-times');
            });

            $("#becomeInstructorForm").validate({
                rules: {
                    first_name: {
                        required: true
                    },
                    last_name: {
                        required: true
                    },
                    contact_email:{
                        required: true,
                        email:true
                    },
                    telephone: {
                        required: true
                    },
                    paypal_id:{
                        required: true,
                        email:true
                    },
                    biography: {
                        required: true
                    },
                },
                messages: {
                    first_name: {
                        required: 'The first name field is required.'
                    },
                    last_name: {
                        required: 'The last name field is required.'
                    },
                    contact_email: {
                        required: 'The contact email field is required.',
                        email: 'The contact email must be a valid email address.'
                    },
                    telephone: {
                        required: 'The telephone field is required.'
                    },
                    paypal_id: {
                        required: 'The paypal id field is required.',
                        email: 'The paypal id must be a valid email address.'
                    },
                    biography: {
                        required: 'The biography field is required.'
                    },
                }
            });
        });

        let socket = new WebSocket("ws://localhost:5003");

        socket.onopen = function(e) {
            console.log("[open] Connection established");
            console.log("Sending to server");
            @if(Auth::user() && Auth::user()->hasRole('instructor'))
            socket.send("instructor:{{ Auth::user()->instructor->id }}");
            @guest
            socket.send("user:{{ Auth::user()->id }}");
            @endif
        };

        socket.onmessage = function(event) {
            console.log(`[message] Data received from server: ${event.data}`);
            $("#urgentLessons").html(event.data);
            $("#notificationMenuButtonRight").find("svg").removeClass("d-none");
            if (parseInt(event.data) > 0) {
                $("#notificationMenuButtonRight").find("svg").eq(0).addClass("d-none");
            } else {
                $("#notificationMenuButtonRight").find("svg").eq(1).addClass("d-none");
            }            
        };

        socket.onclose = function(event) {
            if (event.wasClean) {
                console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`);
            } else {
                // e.g. server process killed or network down
                // event.code is usually 1006 in this case
                console.log('[close] Connection died');
            }
        };

        socket.onerror = function(error) {
            console.log(`[error] ${error.message}`);
        };
    </script>
    @yield('javascript')
</html>