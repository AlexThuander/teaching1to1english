@extends('layouts.frontend.index')
@section('content')
<!-- content start -->
    <div class="container-fluid p-0 home-content">
        <!-- banner start -->
        <div class="subpage-slide-blue">
            <div class="container">
                <h1>My Lessons</h1>
            </div>
        </div>
        <!-- banner end -->

        <!-- breadcrumb start -->
        <div class="breadcrumb-container">
            <div class="container">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Lessons</li>
              </ol>
            </div>
        </div>
        
        <!-- breadcrumb end -->

        <!-- course list start -->
        <div class="container" id="my-courses">
            <div class="row">
            @if(count($lessons)> 0 )
            @foreach($lessons as $lesson)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="course-block mx-auto">
                        <a href="" class="c-view">
                            <main>
                                <img src="@if(Storage::exists($lesson->instructor_image)){{ Storage::url($lesson->instructor_image) }}@else{{ asset('backend/assets/images/female_profile.png') }}@endif">
                                <div class="col-md-12"><h6 class="course-title">{{ $lesson->start_datetime }}&nbsp;-&nbsp;@php echo date_format(date_create($lesson->end_datetime), "H:i:s") @endphp</h6></div>
                                
                                <div class="instructor-clist">
                                    <div class="col-md-12">
                                        <i class="fa fa-chalkboard-teacher"></i>&nbsp;
                                        <span><b>{{ $lesson->first_name.' '.$lesson->last_name }}</b></span>
                                    </div>
                                </div>
                            </main>
                            <footer>
                                <div class="c-row">
                                    <div class="col-md-6 col-sm-6 col-6">
                                        <h5 class="course-price">&nbsp;<s></s></h5>
                                    </div>
                                    <div class="col-md-5 offset-md-1 col-sm-5 offset-sm-1 col-5 offset-1">
                                        <star class="course-rating stars-box">
                                        @php
                                            $full_stars = (int)$lesson->instructor_stars;
                                            $half_stars = $lesson->instructor_stars - $full_stars;
                                            if ($half_stars >= 0.75) {
                                                $half_stars = 0;
                                                $full_stars++;
                                            } else if ($half_stars >= 0.35 && $half_stars < 0.75) {
                                                $half_stars = 1;
                                            } else {
                                                $half_stars = 0;
                                            }
                                            $empty_stars = 5 - $full_stars - $half_stars;
                                        @endphp
                                        @while($full_stars--)
                                        <span class="full-star"></span>
                                        @endwhile
                                        @while($half_stars--)
                                        <span class="half-star"></span>
                                        @endwhile
                                        @while($empty_stars--)
                                        <span class="empty-star"></span>
                                        @endwhile
                                        </star>
                                    </div>
                                </div>
                            </footer>
                        </a>    
                        </div>
                    </div>
                @endforeach
            @else
                <article class="container not-found-block">
                    <div class="row">
                    <div class="col-12 not-found-col">
                            <span><b>2<span class="blue">0</span>4</b></span>
                            <h3>Sorry! No courses added to your account</h3>
                            <a href="{{ route('home') }}" class="btn btn-ulearn-cview mt-3">Explore Courses</a>
                    </div>
                    </div>
                </article>
            @endif                             
            </div>
            </div>
            
        </div>
        <!-- course list end -->
@endsection