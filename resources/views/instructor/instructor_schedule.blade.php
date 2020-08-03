@extends('layouts.backend.index')
@section('head')
<link href="{{ asset('frontend/vendor/fullcalendar/main.css') }}" rel='stylesheet' />
<script src="{{ asset('frontend/vendor/fullcalendar/main.js') }}"></script>
<script src="{{ asset('backend/vendor/moment/moment.min599c.js') }}"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
  var initialTimeZone = 'local';
  var timeZoneSelectorEl = document.getElementById('time-zone-selector');
  var loadingEl = document.getElementById('loading');
  var calendarEl = document.getElementById('schedule');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'timeGridWeek'
    },
    initialDate: (new Date()).toISOString().split('T')[0],
    initialView: 'timeGridWeek',
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
    selectOverlap: false,
    selectAllow: function(e) {
      return true;
    },
    select: function(arg) {
      new_event_id = (new Date()).toString();

      calendar.addEvent({
        id: new_event_id,
        start: arg.start,
        end: arg.end,
        color: '#257e4a'
      })
      
      calendar.unselect();

      $.post("{{ route('instructor.schedule.save') }}", {start: arg.startStr, end: arg.endStr}, function(){
      })
    },
    eventClick: function(arg) {
      $.post("{{ route('instructor.schedule.delete') }}", {start: arg.event.startStr, end: arg.event.endStr}, function(){
        arg.event.remove();
      })     
    },
    eventRemove: function(info) {
      
    },
    events: {
      url: "{{ route('instructor.schedule.get') }}",
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

    .left { float: left }
    .right { float: right }
    .clear { clear: both }
    
</style>
@endsection

@section('content')

<div class="page-header">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Teacher Schedule</li>
  </ol>
  <h1 class="page-title">Teacher Schedule</h1>
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
          <select id='time-zone-selector' class="form-control">
            <option value='local' selected>local</option>
            <option value='UTC'>UTC</option>
          </select>
        </div>

        <div class='right'>
          <span id='loading'>loading...</span>
          <span id='script-warning'><code>{{ route('instructor.schedule.get') }}</code> must be running.</span>
        </div>

        <div class='clear'></div>

      </div>

      <div id='schedule'></div>
                
    </div>
  </div>
  <!-- End Panel Basic -->
</div>

@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection