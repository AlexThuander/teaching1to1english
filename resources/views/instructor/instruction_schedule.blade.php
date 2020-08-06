@extends('layouts.backend.index')
@section('head')
<link href="{{ asset('backend/vendor/fullcalendar/main.css') }}" rel='stylesheet' />
<script src="{{ asset('backend/vendor/fullcalendar/main.js') }}"></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var initialTimeZone = 'local';
    var timeZoneSelectorEl = document.getElementById('time-zone-selector');
    var loadingEl = document.getElementById('loading');
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      timeZone: initialTimeZone,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      initialDate: (new Date()).toISOString().split('T')[0],
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
      events: {
        url: "{{ route('instruction.schedule.get') }}",
        method: "GET",
        extraParams: {localTimeZone: moment.tz.guess()},
        failure: function() {
          document.getElementById('script-warning').style.display = 'inline'; // show
        }
      },
      loading: function(bool) {
        if (bool) {
          loadingEl.style.display = 'inline'; // show
        } else {
          loadingEl.style.display = 'none'; // hide
        }
      },
      dateClick: function(arg) {
        console.log('dateClick', calendar.formatIso(arg.date));
      },
      select: function(arg) {
        console.log('select', calendar.formatIso(arg.start), calendar.formatIso(arg.end));
      }
    });

    calendar.render();

    // load the list of available timezones, build the <select> options
    // it's HIGHLY recommended to use a different library for network requests, not this internal util func
    FullCalendar.requestJson('GET', "{{ route('fullcalendar.getTimeZones') }}", {}, function(timeZones) {

      timeZones.forEach(function(timeZone) {
        var optionEl;

        if (timeZone !== 'UTC') { // UTC is already in the list
          optionEl = document.createElement('option');
          optionEl.value = timeZone;
          optionEl.innerText = timeZone;
          timeZoneSelectorEl.appendChild(optionEl);
        }
      });
    }, function() {
      // TODO: handle error
    });

    // when the timezone selector changes, dynamically change the calendar option
    timeZoneSelectorEl.addEventListener('change', function() {
      calendar.setOption('timeZone', this.value);
    });
  });

</script>
<style>

  .left { float: left }
  .right { float: right }
  .clear { clear: both }

  #script-warning, #loading { display: none }
  #script-warning { font-weight: bold; color: red }

  #calendar {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 10px;
  }

  .tzo {
    color: #000;
  }

  .fc-day-today {
      background-color:inherit !important;
  }
</style>

@endsection

@section('content')

<div class="page-header">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Lesson Schedule</li>
  </ol>
  <h1 class="page-title">Lesson Schedule</h1>
</div>

<div class="page-content">

  <div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            
        </div>
    </div>
    
    <div class="panel-body">
      
      <div id='top'>

        <div class='col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 input-group'>
          <span class="input-group-addon">Timezone:</span>
          <select id='time-zone-selector' class="form-control">
            <option value='local' selected>local</option>
          </select>
        </div>

        <div class='right'>
          <span id='loading'>loading...</span>
        </div>

        <div class='clear'></div>

      </div>

      <div id='calendar'></div>
                
    </div>
  </div>
  <!-- End Panel Basic -->
</div>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function()
{

});
</script>
@endsection