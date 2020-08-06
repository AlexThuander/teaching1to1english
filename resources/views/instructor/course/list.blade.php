@extends('layouts.backend.index')
@section('content')
<div class="page-header">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Lessons</li>
  </ol>
  <h1 class="page-title">Lessons</h1>
</div>

<div class="page-content">

<div class="panel">
        <div class="panel-heading">
            <div class="panel-title" style="padding-top: 50px;">
              <!-- <a href="{{ route('instructor.course.info') }}" class="btn btn-success btn-sm"><i class="icon wb-plus" aria-hidden="true"></i> Add Course</a> -->
              <div class="panel-actions">
              <form method="GET" action="{{ route('instructor.course.list') }}" id="instructorCourseListForm">
                  <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ Request::input('search') }}">
                    <input type="hidden" name="tzname">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-original-title="Search"><i class="icon wb-search" aria-hidden="true"></i></button>
                      <a href="{{ route('instructor.course.list') }}" class="btn btn-danger" data-toggle="tooltip" data-original-title="Clear Search"><i class="icon wb-close" aria-hidden="true"></i></a>
                    </span>
                  </div>
              </form>
              </div>
            </div>          
        </div>
        
        <div class="panel-body">
          <table class="table table-hover table-striped w-full">
            <thead>
              <tr>
                <th>Sl.no</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($courses as $course)
              <tr>
                <td>{{ $course->id }}</td>
                <td>{{ $course->first_name }}</td>
                <td>{{ $course->last_name }}</td>
                <td>{{ $course->start_time }}&nbsp;-&nbsp;@php echo date_format(date_create($course->end_time), "H:i:s") @endphp</td>
                <td>
                  @if($course->status == 'incomplete' && $course->expired == 0)
                  <span class="badge badge-primary">Active</span>
                  @elseif($course->status == 'complete')
                  <span class="badge badge-success">Complete</span>
                  @else
                  <span class="badge badge-danger">Expired</span>
                  @endif
                </td>
                <td>
                  @if($course->status == 'incomplete' && $course->expired == 0)
                  <a href="{{ route('instructor.opentok.open', $course->id) }}" class="btn btn-xs btn-icon btn-inverse btn-round" data-toggle="tooltip" data-original-title="Edit" >
                    <i class="icon wb-pencil" aria-hidden="true"></i>
                  </a>
                  @else
                  <a href="{{ url('instructor-course-delete/'.$course->id) }}" class="delete-record btn btn-xs btn-icon btn-inverse btn-round" data-toggle="tooltip" data-original-title="Delete" >
                    <i class="icon wb-trash" aria-hidden="true"></i>
                  </a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          
          <div class="float-right">
            {{ $courses->appends(['search' => Request::input('search')])->links() }}
          </div>
          
          
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