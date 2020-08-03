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
        url: "{{ route('fullcalendar.getEvents') }}",
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

      eventTimeFormat: { hour: 'numeric', minute: '2-digit', timeZoneName: 'short' },

      dateClick: function(arg) {
        console.log('dateClick', calendar.formatIso(arg.date));
      },
      select: function(arg) {
        console.log('select', calendar.formatIso(arg.start), calendar.formatIso(arg.end));
      },
      events: JSON.parse('<?php echo str_replace('&quot;', '"', $events) ?>')
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

  #top {
    background: #eee;
    border-bottom: 1px solid #ddd;
    padding: 0 10px;
    line-height: 40px;
    font-size: 12px;
  }
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

        <div class='left'>
          Timezone:
          <select id='time-zone-selector'>
            <option value='local' selected>local</option>
            <option value='UTC'>UTC</option>
          </select>
        </div>

        <div class='right'>
          <span id='loading'>loading...</span>
          <span id='script-warning'><code>{{ route('fullcalendar.getEvents') }}</code> must be running.</span>
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