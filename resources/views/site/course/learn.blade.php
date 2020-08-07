@extends('layouts.frontend.index')
@section('content')
<link rel="stylesheet" href="{{ asset('frontend/vendor/rating/rateyo.css') }}">
<!-- content start -->
<div class="container-fluid p-0 home-content">
    <!-- banner start -->
    <div class="subpage-slide-blue">
        <div class="container">
            <h1>Lesson</h1>
        </div>
    </div>
    <!-- banner end -->
    
    <!-- breadcrumb start -->
        <div class="breadcrumb-container">
            <div class="container">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><button type="submit" form="goMyCoursesForm">My Lessons</button></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $course->id }}</li>
              </ol>
            </div>
            <form action="{{ route('my.courses') }}" id="goMyCoursesForm">
                <input type="hidden" name="tzname">
            </form>
        </div>
    
    <!-- breadcrumb end -->
    
    <div class="container">
        <div class="row mt-4">
            <!-- course block start -->
            <div class="col-xl-9 col-lg-9 col-md-8">
                    <div class="cv-course-container">
                    <h4></h4>
                    <div class="instructor-clist m-0">
                        <div class="col-md-12 p-0 m-0">
                            <i class="fa fa-chalkboard-teacher"></i>&nbsp;
                            <span>Created by <b>{{ $course->first_name }} {{ $course->last_name }}</b></span>
                        </div>
                    </div>
                    <div class="row cv-header">
                        
                        <div class="col-md-12">
                            <div class="modal-content">
                            <div class="modal-header bi-header ">
                                <h5 class="col-12 modal-title text-center bi-header-seperator-head">Rate the Lesson</h5>
                            </div>
                                
                            <div class="becomeInstructorForm">
                                <form id="rateCourseForm" class="form-horizontal" method="POST" action="{{ route('course.rate') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="instructor_id" value="{{ $course->instructor_id }}">
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <input type="hidden" name="rating" id="rating" value="{{ $course_rating->rating }}">
                                    <input type="hidden" name="rating_id" value="{{ $course_rating->id }}">
                                    <div class="px-4 py-2">
                                        <div class="form-group">
                                            <label>Your Rating</label>
                                            <div class="row">
                                                <div class="col-7 pl-2">
                                                    <div id="rateYo"></div>
                                                </div>
                                                <div class="col-5">
                                                    @if($course_rating->id)
                                                        <a class="btn btn-sm btn-block delete-review delete-record" href="{{ route('delete.rating', $course_rating->id) }}">Delete Review</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                        
                                        <div class="form-group">
                                            <label>Your Review</label>
                                            <textarea class="form-control form-control" placeholder="Comments" name="comments">{{ $course_rating->comments }}</textarea>
                                        </div>
                        
                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-lg btn-block login-page-button">{{ $course_rating->id ? 'Update' : 'Add' }} Review</button>
                                        </div>
                        
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- course block end -->

            <!-- course sidebar start -->
            <div class="col-xl-3 col-lg-3 col-md-4 d-none d-md-block">
                <section class="course-feature">
                    <header>
                        <h6>LESSON FEATURES</h6>
                    </header>

                    <div class="cf-pricing">
                        <span>PRICING:</span>
                        <button class="cf-pricing-btn btn">PAID</button>
                    </div>

                    <ul class="list-unstyled cf-pricing-li">
                        <li><i class="far fa-user"></i>Level: {{ $course->level }}</li>
                        <li><i class="far fa-clock"></i>Duration: {{ $course->duration }} min</li>
                        <li><i class="fas fa-bullhorn"></i>Type: {{ $course->lesson_type }}</li>
                        <li><i class="far fa-address-card"></i>Certificate of Completion</li>
                    </ul>
                </section>

            </div>
            <!-- course sidebar end -->
        </div>
    </div>
    
<!-- content end -->

@endsection

@section('javascript')
<script src="{{ asset('frontend/vendor/rating/rateyo.js') }}"></script>
<script type="text/javascript">
function toggleIcon(e) 
{
    $(e.target)
        .prev('.card-header')
        .find(".accordian-icon")
        .toggleClass('fa-plus fa-minus');
}
$('.accordion').on('hidden.bs.collapse', toggleIcon);
$('.accordion').on('shown.bs.collapse', toggleIcon);

// lightbox init
function initFancybox() {
"use strict";

$('a.lightbox, [data-fancybox]').fancybox({
  parentEl: 'body',
  margin: [50, 0]
});
}

$(document).ready(function()
{
    initFancybox();

    $("#rateYo").rateYo({
        @if($course_rating->id)
            rating: '{{ $course_rating->rating }}',
        @endif
        halfStar: true,
        ratedFill: "#00a500",
        starWidth: "25px",
        onChange: function (rating, rateYoInstance) {
            $('#rating').val(rating);
        }
    });
});
</script>
@endsection