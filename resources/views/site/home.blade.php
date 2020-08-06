@extends('layouts.frontend.index')
@section('head')
<link href="{{ asset('frontend/vendor/fullcalendar/main.css') }}" rel='stylesheet' />
<script src="{{ asset('frontend/vendor/fullcalendar/main.js') }}"></script>
<script>

var g_remain_events_count = 0;
var g_added_events = new Array();

function diffDateTime(first, second) {
    return first.getTime() - second.getTime();
}

function createSchedule() {
    var calendarEl = document.getElementById('schedule');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek'
      },
      initialDate: (new Date()).toISOString().split('T')[0],
      initialView: 'timeGridWeek',
      firstDay: 1,
      navLinks: true, // can click day/week names to navigate views
      businessHours: false, // display business hours
      editable: false,
      eventStartEditable: false,
      eventDurationEditable: false,
      allDaySlot: false,
      eventOverlap: false,
      slotLabelFormat: {hour: 'numeric', minute: '2-digit', hour12: false},
      eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false
      },
      selectable: true,
      selectConstraint: 'availableForLesson',
      selectAllow: function(e) {
        if (e.end.getTime() / 1000 - e.start.getTime() / 1000 <= 1800 * $('#bookLessonType').val()) {
            return true;
        }
      },
      select: function(arg) {
        if (g_remain_events_count == 0) return;

        delta = 1000 * 1800 * ($('#bookLessonType').val() - 1);
        
        var events = calendar.getEvents();
        
        for (var i = 0; i < events.length; i++) {
            if (events[i].groupId != 'availableForLesson' && events[i].constraint != 'availableForLesson') continue;
            if (events[i].groupId == 'availableForLesson' && diffDateTime(events[i].end, arg.start) >= 0 && diffDateTime(events[i].end, arg.start) <= delta) {
                //alert('group')
                return;
            }
            if ( events[i].constraint == 'availableForLesson' && (diffDateTime(events[i].start, arg.start) <= delta) && (diffDateTime(events[i].end, arg.start) > delta) ) {
                //alert('const')
                return;
            }
        }
                
        end_time = new Date();
        end_time.setTime(arg.start.getTime() + delta + 1000 * 1800);
        new_event_id = (new Date()).toString();

        calendar.addEvent({
            id: new_event_id,
            start: arg.start,
            end: end_time,
            constraint: 'availableForLesson',
            color: '#257e4a'
        })
        
        calendar.unselect();
        
        g_added_events.push({id: new_event_id, start: arg.start, end: end_time});

        if (--g_remain_events_count == 0) $('#btnLastOptions').removeClass('disabled');
      },
      eventClick: function(arg) {
          g_added_events.forEach(function(ev, index) {
            if (ev.id != arg.event.id) return;
            g_added_events.splice(index, 1);
          })
          arg.event.remove();
      },
      eventRemove: function(info) {
        g_remain_events_count++;
      },
      events: {
        url: "{{ route('instructor.schedule.booked.get') }}",
        method: "GET",
        extraParams: {instructor_id: $('#bookInstructorId').val(), tzname: moment.tz.guess()},
        failure: function() {
            document.getElementById('script-warning').style.display = 'inline'; // show
        }
      },
    });

    calendar.render();
}

</script>
<style>

    body {
        background-color: #edeff0;
    }

    .instructor-block {
        background-color: #edeff0;
    }

    #schedule {
        max-width: 768px;
        margin: 20px auto;
    }

    a.disabled {
        pointer-events: none;
    }

    .modal-header img {
        cursor: pointer;
    }
    
    .fc-day-today {
        background-color:inherit !important;
    }

</style>
@endsection

@section('content')
<!-- banner start -->
<div class="homepage-slide-blue home-content">
    <form method="GET" action="{{ route('instructor.list') }}" id="selTeacherForm"></form>
    <div class="container">
        <div class="row">
            <div class="col-2 col-sm-1 col-md-2 col-lg-1 col-xl-1 mt-4 text-right"><label for="selCategory">Types:</label></div>
            <div class="col-10 col-sm-11 col-md-4 col-lg-3 col-xl-3 mt-3">
                @php 
                    $categories = SiteHelpers::active_categories();
                @endphp
                <select class="form-control" id="selCategory" name="category_id" form="selTeacherForm" onchange="this.form.submit();">
                    <option value="0">All</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if(isset($category_search) && $category->id == $category_search) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-2 col-sm-1 col-md-2 col-lg-1 col-xl-1 mt-4 text-right"><label for="selInstructionLevel">Levels:</label></div>
            <div class="col-10 col-sm-11 col-md-4 col-lg-3 col-xl-3 mt-3">
                @php 
                    $instruction_levels = SiteHelpers::instruction_levels();
                @endphp
                <select class="form-control" id="selInstructionLevel" name="instruction_level_id" form="selTeacherForm" onchange="this.form.submit();">
                    <option value="0">All</option>
                    @foreach ($instruction_levels as $instruction_level)
                    <option value="{{ $instruction_level->id }}" @if(isset($instruction_level_id) && $instruction_level->id == $instruction_level_id) selected @endif>{{ $instruction_level->level }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="searchbox-contrainer col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 mx-auto">
                <input name="keyword" type="text" class="searchbox d-none d-sm-inline-block" placeholder="Search by name or keyword" form="selTeacherForm"><input name="keyword" type="text" class="searchbox d-inline-block d-sm-none" placeholder="Search for name or keyword"><button type="submit" class="searchbox-submit" form="selTeacherForm"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
<!-- banner end -->

<!-- content start -->
<div class="container-fluid p-0">
    
    <!-- instructor block start -->
    <article class="instructor-block">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center seperator-head mt-3">
                    <p style="font-size: 20px;"><strong>{{ count($instructors) }}</strong> teachers found</p>
                </div>
            </div>
            
            @foreach ($instructors as $instructor)
            <div class="row mt-4 mb-4 instructor-box mx-auto">
                <div class="col-md-4 col-lg-2 col-xl-3 pt-4 pb-4">
                    <a href="{{ route('instructor.view', $instructor->instructor_slug) }}">
                        <img src="@if(Storage::exists($instructor->instructor_image)){{ Storage::url($instructor->instructor_image) }}@else{{ asset('backend/assets/images/female_profile.png') }}@endif" id="instructorPhoto" style="width: 100%;">
                    </a>
                    <input type="hidden" class="instructor-id" value="{{ $instructor->id }}">
                </div>
                <div class="col-md-8 col-lg-4 pt-4 pb-4">
                    <h4 class="instructor-title">{{ $instructor->first_name.' '.$instructor->last_name }}</h4>
                    <div class="instructor-box-stars">
                        <div class="stars-box">
                            @php
                                $full_stars = (int)$instructor->instructor_stars;
                                $half_stars = $instructor->instructor_stars - $full_stars;
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
                            <span class="number">{{ $instructor->instructor_stars }}</span>
                        </div>
                        <div class="instructor-price-rate">
                            <span class="instructor-price">{{ $instructor->instructor_price }}</span>$ per hour
                        </div>
                    </div>
                    <div class="instructor-activity">
                        <div class="instructor-students">
                            <i class="fa fa-users"></i>
                            <span> {{ $instructor->student_count }} </span> active students -&nbsp;
                        </div>
                        <div class="instructor-lessons">
                            <span> {{ $instructor->lesson_count }} </span> lessons
                        </div>
                    </div>
                    <div class="instructor-buttons">
                        @guest
                        <a class="btn btn-ulearn btn-lg btn-block btn-book-lesson" href="{{ route('login') }}">Book a lesson</a>
                        @else
                        <button class="btn btn-ulearn btn-lg btn-block btn-book-lesson" data-toggle="modal">Book a lesson</button>
                        @endguest
                        <a class="btn btn-outline-ulearn btn-lg btn-block" href="{{ route('instructor.view', $instructor->instructor_slug) }}">View detail</a>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-5 instructor-box-right">
                    <!-- Nav pills -->
                    <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#video-{{ $instructor->id }}">Video</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#intro-{{ $instructor->id }}">Intro</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#calendar-{{ $instructor->id }}" onclick="selCalendar({{ $instructor->id }})">Calendar</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="video-{{ $instructor->id }}">
                            <video width="320" height="240" style="width: 100%;" controls>
                                <source src="{{ $instructor->instructor_video }}" type="video/mp4">
                                Your browser does not support the video tag.
                              </video>
                        </div>
                        <div class="tab-pane container fade" id="intro-{{ $instructor->id }}">
                            <p>{!! mb_strimwidth($instructor->biography, 0, 120, ".....") !!}</p>
                        </div>
                        <div class="tab-pane container fade" id="calendar-{{ $instructor->id }}">
                            
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- pagination start -->
            <div class="row float-right mt-5">
                {{ $instructors->appends($_GET)->links() }}
            </div>
            <!-- pagination end -->
        </div>
    </article>
    <!-- instructor block end -->

</div>
<!-- content end -->

@guest
@else
<!-- The Modal -->
<div class="modal book-modal" id="bookModal">
  <div class="modal-overlay"></div>
  <div class="modal-dialog book-modal-dialog modal-bottom">
    <div class="modal-content bookflow-modal modal-bottom">

      <!-- Modal Header -->
      <div class="modal-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1 col-1">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="d-none" data-toggle="pill" href="#chooseLesson" id="backChooseLesson">
                            <img src="{{ asset('frontend/img/left.png') }}" alt="back" width="10" height="20"></a>
                            <a class="d-none" data-toggle="pill" href="#planSchedule" id="backPlanSchedule">
                            <img src="{{ asset('frontend/img/left.png') }}" alt="back" width="10" height="20"></a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-10 col-10"><h4 class="modal-title text-center">Lesson options</h4></div>
                <div class="col-md-1 col-1"><button type="button" class="close" data-dismiss="modal" style="outline: none;">&times;</button></div>
            </div>
        </div>  
      </div>
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="optionsProgressBar" style="width:33.33%"></div>
      </div>

      <!-- Modal body -->
      <div class="modal-body book-modal-body">
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active" id="chooseLesson">
                <div class="lesson-options">
                    <div class="book-courses-list">
                        <div class="book-course-options">
                            <h2><span>30 min</span><img src="{{ asset('frontend/img/down-arrow.png') }}" alt="down arrow"></h2>
                            <div class="book-course-option" data-type="1" data-count="1">
                                <div class="book-course-lessons"><span>1 Lesson</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p></div>
                            </div>
                            <div class="book-course-option" data-type="1" data-count="5">
                                <div class="book-course-lessons"><span>5 Lessons</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p><p class="book-course-price-discount"><span>SAVE 5%</span></p></div>
                            </div>
                            <div class="book-course-option" data-type="1" data-count="10">
                                <div class="book-course-lessons"><span>10 Lessons</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p><p class="book-course-price-discount"><span>SAVE 10%</span></p></div>
                            </div>
                        </div>
                        <div class="book-course-options">
                            <h2><span>60 min</span><img src="{{ asset('frontend/img/down-arrow.png') }}" alt="down arrow"></h2>
                            <div class="book-course-option" data-type="2" data-count="1">
                                <div class="book-course-lessons"><span>1 Lesson</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p></div>
                            </div>
                            <div class="book-course-option" data-type="2" data-count="5">
                                <div class="book-course-lessons"><span>5 Lessons</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p><p class="book-course-price-discount"><span>SAVE 5%</span></p></div>
                            </div>
                            <div class="book-course-option" data-type="2" data-count="10">
                                <div class="book-course-lessons"><span>10 Lessons</span></div>
                                <div class="book-course-price"><p>$ <span class="book-real-price"></span></p><p class="book-course-price-discount"><span>SAVE 10%</span></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane container fade" id="planSchedule">
                <div class="book-calendar-head">
                    <div class="book-calendar-labels">
                        <span class="caption book-calendar-hint"><i class="book-time-available"></i>
                            <span>Available</span>
                        </span>
                        <span class="caption book-calendar-hint"><i class="book-by-others"></i>
                            <span>Booked</span>
                        </span>
                        <span class="caption book-calendar-hint"><i class="book-time-not-available"></i>
                            <span>Not available</span>
                        </span>
                        <span class="caption book-calendar-hint"><i class="book-time-selected"></i>
                            <span>Booked by you</span>
                        </span>
                    </div>
                    <span class="caption book-calendar-hint">
                        <span class="user-based-timezone"><span>Based on your timezone</span> (UTC<span id="localTimeZone"></span>)</span>
                    </span>
                </div>
                <div id="schedule"></div>
            </div>
            <div class="tab-pane container fade" id="confirmPurchase">
                
                <article class="container mt-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="underline-heading mb-4"></h3>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-5 col-12">
                            <img src="" id="instructorModalPhoto" style="width: 100%; height: auto;">
                        </div>
                        <div class="col-md-7 col-12 pl-4">
                            <h2 class="mb-xl-0 mt-1" id="confirmTeacher"></h2>
                            <div class="instructor-clist mb-0 mt-1">
                                <div>
                                    <i class="fa fa-chalkboard-teacher"></i>&nbsp;
                                    <span><b id="confirmLessonType"></b> min - <b id="confirmLessonCount"></b> lessons</span>
                                </div>
                            </div>
                            <div class="instructor-clist mb-0 mt-1 d-sm-block">
                                <div class="ml-1">
                                    <i class="far fa-bookmark"></i>&nbsp;&nbsp;
                                    <span><b>Date and Time</b></span>
                                </div>
                            </div>
                            <div id="confirmSchedule">
                            </div>
                            <div class="instructor-clist">
                                <h4 class="c-price-checkout" id="confirmPriceCheckout"></h4> $
                            </div>
                        </div>

                    </div>        
                            
                    <div class="row">        
                        <div class="col-xl-7 offset-xl-2 col-lg-8 offset-lg-2 col-md-9 offset-md-2 col-sm-9 offset-sm-2 col-11 offset-1">
                        <form method="POST" action="{{ route('payment.form') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                            <input type="hidden" name="instructor_id" id="bookInstructorId" value="0">
                            <input type="hidden" name="payment_method" value="paypal_express_checkout">
                            <input type="hidden" name="lesson_type" id="bookLessonType">
                            <input type="hidden" name="lesson_count" id="bookLessonCount">
                            <input type="hidden" name="lesson_amount" id="bookLessonAmount">
                            <input type="hidden" name="lesson_schedule" id="bookLessonSchedule">

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-lg btn-block social-btn facebook-btn text-center">
                                    <i class="fab fa-paypal"></i>
                                    <span style="font-size: 20px;">
                                    Pay with Paypal Account
                                    </span>
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>                          
                </article>
            </div>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <ul class="nav">
            <li class="nav-item">
                <a class="btn btn-danger bookflow-next-btn btn-gradient d-none" data-toggle="pill" href="#chooseLesson" id="btnInitModal"> </a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger bookflow-next-btn btn-gradient disabled" data-toggle="pill" href="#planSchedule" id="btnNextOptions">Next</a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger bookflow-next-btn btn-gradient disabled d-none" data-toggle="pill" href="#confirmPurchase" id="btnLastOptions">Next</a>
            </li>
        </ul>        
      </div>
    </div>
  </div>
</div>

@endguest

@endsection

@section('javascript')
<script>
    var g_lesson_options_changed = true;

    $('#backChooseLesson').click(function() {
        $('#optionsProgressBar').css('width', '33.33%');
        $(this).addClass('d-none');
        $('#btnLastOptions').addClass('d-none');
        $('#btnNextOptions').removeClass('d-none');
        $('#btnNextOptions').removeClass('disabled');
        $('#btnNextOptions').removeClass('active');
        $('#btnNextOptions').removeClass('show');
        $('.modal-title').text('Lesson options');
    })

    $('#backPlanSchedule').click(function() {
        $('#optionsProgressBar').css('width', '66.67%');
        $(this).addClass('d-none');
        $('#backChooseLesson').removeClass('d-none');
        $('#backChooseLesson').removeClass('active');
        $('#backChooseLesson').removeClass('show');
        $('#btnLastOptions').removeClass('invisible');
        $('#btnLastOptions').removeClass('disabled');
        $('#btnLastOptions').removeClass('active');
        $('#btnLastOptions').removeClass('show');
        $('.modal-title').text('Schedule your lessons');
    })

    $('.btn-book-lesson').click(function() {
        
        g_lesson_options_changed = true;

        instructor_price = parseFloat($(this).parents('.instructor-box').eq(0).find('.instructor-price').eq(0).text());
        book_course_options = $('#chooseLesson').find('.book-course-options');
        book_course_option = book_course_options.eq(0).find('.book-real-price');
        book_course_option.eq(0).text(Math.floor(instructor_price / 2 * 100) / 100);
        book_course_option.eq(1).text(Math.floor(instructor_price / 2 * 95 * 5) / 100);
        book_course_option.eq(2).text(Math.floor(instructor_price / 2 * 90 * 10) / 100);
        book_course_option = book_course_options.eq(1).find('.book-real-price');
        book_course_option.eq(0).text(Math.floor(instructor_price * 100) / 100);
        book_course_option.eq(1).text(Math.floor(instructor_price * 95 * 5) / 100);
        book_course_option.eq(2).text(Math.floor(instructor_price * 90 * 10) / 100);

        $('#backChooseLesson').addClass('d-none');
        $('#backPlanSchedule').addClass('d-none');
        
        $('#btnInitModal').click();
        $('.book-course-option').removeClass('book-course-options-active');

        $('#instructorModalPhoto').attr('src', $(this).parents('.instructor-box').eq(0).find('img').eq(0).attr('src'));
        $('#confirmTeacher').text($(this).parents('.instructor-box').eq(0).find('.instructor-title').eq(0).text());
        $('#bookInstructorId').val($(this).parents('.instructor-box').eq(0).find('.instructor-id').eq(0).val());

        if (!$('#btnNextOptions').hasClass('disabled')) {
            $('#btnNextOptions').addClass('disabled');
        }
        if ($('#btnNextOptions').hasClass('d-none')) {
            $('#btnNextOptions').removeClass('d-none');
        }
        if ($('#btnLastOptions').hasClass('invisible')) {
            $('#btnLastOptions').removeClass('invisible');
        }
        if (!$('#btnLastOptions').hasClass('disabled')) {
            $('#btnLastOptions').addClass('disabled');
        }
        if (!$('#btnLastOptions').hasClass('d-none')) {
            $('#btnLastOptions').addClass('d-none');
        }
        $('#optionsProgressBar').css('width', '33.33%');

        setTimeout(() => {
            $('#bookModal').modal();
        }, 500);
    })

    $('.book-course-option').click(function() {
        if ($(this).hasClass('book-course-options-active')) return;
        
        $('#schedule').html('');

        $('.book-course-option').removeClass('book-course-options-active');
        $(this).addClass('book-course-options-active');
        $('#btnNextOptions').removeClass('disabled');
        if (!$('#btnLastOptions').hasClass('disabled')) {
            $('#btnLastOptions').addClass('disabled');
        }
        
        $('#bookLessonType').val($(this).data('type'));
        $('#bookLessonCount').val($(this).data('count'));

        $('#confirmLessonType').text($('#bookLessonType').val() == 1 ? 30 : 60);
        $('#confirmLessonCount').text($('#bookLessonCount').val());
        $('#confirmPriceCheckout').html($(this).find('.book-real-price').eq(0).text());
        $('#bookLessonAmount').val($(this).find('.book-real-price').eq(0).text());

        g_lesson_options_changed = true;
        g_remain_events_count = $(this).data('count');
        g_added_events.splice(0, g_added_events.length);
    })

    $('#btnNextOptions').click(function() {
        $(this).addClass('d-none');
        $('.modal-title').text('Schedule your lessons');
        $('#btnLastOptions').removeClass('d-none');
        $('#optionsProgressBar').css('width', '66.67%');
        $('#backChooseLesson').removeClass('d-none');
        $('#backChooseLesson').removeClass('active');
        $('#backChooseLesson').removeClass('show');

        if (g_lesson_options_changed) {
            setTimeout(() => {
                createSchedule();
                g_lesson_options_changed = false;
            }, 500);
        }
    })

    $('#btnLastOptions').click(function() {
        $(this).addClass('invisible');
        $('.modal-title').text('Confirm purchase');
        $('#optionsProgressBar').css('width', '100%');
        $('#backChooseLesson').addClass('d-none');
        $('#backPlanSchedule').removeClass('d-none');
        $('#backPlanSchedule').removeClass('active');
        $('#backPlanSchedule').removeClass('show');

        confirm_schedule_content = '';
        g_added_events.forEach(function(ev, index) {
            confirm_schedule_content += '<div class="instructor-clist mb-0 mt-1 ml-4 d-sm-block">';
            confirm_schedule_content += FullCalendar.formatDate(ev.start, {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false}) + ' - ';
            confirm_schedule_content += FullCalendar.formatDate(ev.end, {hour: '2-digit', minute: '2-digit', hour12: false});
            confirm_schedule_content += '</div>';
        })
        $('#confirmSchedule').html(confirm_schedule_content);

        $('#bookLessonSchedule').val(JSON.stringify(g_added_events));
    })

    function selCalendar(instructor_id) {
        $.get("{{ route('instructor.calendar.get')}}", {instructor_id: instructor_id, tzname: moment.tz.guess()}, function(result) {
            $('#calendar-' + instructor_id).html(result);
        })
    }

    $(document).ready(function() {
        $('#localTimeZone').html(new Date().toString().match(/([-\+][0-9]+)\s/)[1]);
    })
</script>
@endsection