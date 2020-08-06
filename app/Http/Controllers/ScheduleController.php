<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Instructor;
use App\Models\LessonProgress;
use App\Models\InstructorAvailableTime;

class ScheduleController extends Controller
{
    public function instructorScheduleList()
    {
        return view('instructor.instructor_schedule');
    }

    public function getInstructorCalendar(Request $request)
    {
        $instructor_id = $request->input('instructor_id');
        $tzname = $request->input('tzname');

        date_default_timezone_set("Europe/London");

        $timespans = array();

        $monday = strtotime("last monday");
        $sunday = strtotime(date("Y-m-d", $monday)." +6 days");

        $available_timespan = array();
        for($i = $monday; $i <= $sunday; $i+=24*3600) {
            $tarday = date('Y-m-d', $i);

            $start = $tarday . ' 06:00:00';
            $start = new \DateTime($start, new \DateTimeZone($tzname));
            $start->setTimezone(new \DateTimeZone('Europe/London'));
            $start= $start->format('Y-m-d H:i:s');

            $end = $tarday . ' 12:00:00';
            $end = new \DateTime($end, new \DateTimeZone($tzname));
            $end->setTimezone(new \DateTimeZone('Europe/London'));
            $end= $end->format('Y-m-d H:i:s');

            $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor_id, $start, $end, $start, $end])[0]->timespan/60;
        }
        $timespans['morning'] = $available_timespan;

        $available_timespan = array();
        for($i = $monday; $i <= $sunday; $i+=24*3600) {
            $tarday = date('Y-m-d', $i);
            
            $start = $tarday . ' 12:00:00';
            $start = new \DateTime($start, new \DateTimeZone($tzname));
            $start->setTimezone(new \DateTimeZone('Europe/London'));
            $start= $start->format('Y-m-d H:i:s');
            
            $end = $tarday . ' 18:00:00';
            $end = new \DateTime($end, new \DateTimeZone($tzname));
            $end->setTimezone(new \DateTimeZone('Europe/London'));
            $end= $end->format('Y-m-d H:i:s');

            $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor_id, $start, $end, $start, $end])[0]->timespan/60;
        }
        $timespans['afternoon'] = $available_timespan;

        $available_timespan = array();
        for($i = $monday; $i <= $sunday; $i+=24*3600) {
            $tarday = date('Y-m-d', $i);
            
            $start = $tarday . ' 18:00:00';
            $start = new \DateTime($start, new \DateTimeZone($tzname));
            $start->setTimezone(new \DateTimeZone('Europe/London'));
            $start= $start->format('Y-m-d H:i:s');

            $end = $tarday . ' 24:00:00';
            $end = new \DateTime($end, new \DateTimeZone($tzname));
            $end->setTimezone(new \DateTimeZone('Europe/London'));
            $end= $end->format('Y-m-d H:i:s');

            $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor_id, $start, $end, $start, $end])[0]->timespan/60;
        }
        $timespans['evening'] = $available_timespan;

        $available_timespan = array();
        for($i = $monday; $i <= $sunday; $i+=24*3600) {
            $tarday = date('Y-m-d', $i);
            
            $start = $tarday . ' 00:00:00';
            $start = new \DateTime($start, new \DateTimeZone($tzname));
            $start->setTimezone(new \DateTimeZone('Europe/London'));
            $start= $start->format('Y-m-d H:i:s');

            $end = $tarday . ' 06:00:00';
            $end = new \DateTime($end, new \DateTimeZone($tzname));
            $end->setTimezone(new \DateTimeZone('Europe/London'));
            $end= $end->format('Y-m-d H:i:s');

            $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor_id, $start, $end, $start, $end])[0]->timespan/60;
        }
        $timespans['night'] = $available_timespan;
        
        return view('site.instructor_calendar', compact('timespans'));
    }

    public function getInstructorSchedule()
    {
        $instructor_available_times = InstructorAvailableTime::where('instructor_id', \Auth::user()->instructor->id)->get();
        
        $events = array();
        foreach($instructor_available_times as $instructor_available_time) {
            $event = new \stdClass();
            $event->id = $instructor_available_time->id;
            $event->start = str_replace(' ', 'T', $instructor_available_time->start_time);
            $event->end = str_replace(' ', 'T', $instructor_available_time->end_time);
            $event->color = '#257e4a';
            $events[] = $event;
        }

        return response()->json($events);
    }

    public function saveInstructorSchedule(Request $request)
    {
        $instructor_available_time = new InstructorAvailableTime();
        $instructor_available_time->instructor_id = \Auth::user()->instructor->id;
        $instructor_available_time->start_time = $request->start;
        $instructor_available_time->end_time = $request->end;
        $instructor_available_time->save();
    
        return $instructor_available_time->id;
    }
    
    public function deleteInstructorSchedule(Request $request)
    {
        $instructor_available_time = InstructorAvailableTime::where('instructor_id', \Auth::user()->instructor->id)
            ->where('start_time', $request->start)
            ->where('end_time', $request->end)
            ->delete();
    
        return response()->json($instructor_available_time);
    }

    public function instructionScheduleList()
    {
        $instructor_id = \Auth::user()->instructor->id;
        $lesson_progresses = LessonProgress::where('instructor_id', $instructor_id)
            ->where('status', 'incomplete')
            ->get();
        
        $events = array();
        foreach($lesson_progresses as $lesson_progress) {
            $event = new \stdClass();
            $event->start = str_replace(' ', 'T', $lesson_progress->start_time);
            $event->end = str_replace(' ', 'T', $lesson_progress->end_time);
            $event->constraint = 'availableForLesson';
            $event->color = '#257e4a';
            $events[] = $event;
        }
        return view('instructor.instruction_schedule')->with('events', json_encode($events));
    }

    public function getBookedSchedule($instructor_id = 0)
    {
        $events = array();

        $instructor_available_times = InstructorAvailableTime::where('instructor_id', $instructor_id)->get();        
        foreach($instructor_available_times as $instructor_available_time) {
            $event = new \stdClass();
            $event->groupId = 'availableForLesson';
            $event->start = str_replace(' ', 'T', $instructor_available_time->start_time);
            $event->end = str_replace(' ', 'T', $instructor_available_time->end_time);
            $event->display = 'background';
            $events[] = $event;
        }

        $other_booked_times = LessonProgress::where('instructor_id', $instructor_id)->where('user_id', '<>', \Auth::user()->id)->get();
        foreach($other_booked_times as $other_booked_time) {
            $event = new \stdClass();
            $event->start = str_replace(' ', 'T', $other_booked_time->start_time);
            $event->end = str_replace(' ', 'T', $other_booked_time->end_time);
            $event->display = 'background';
            $event->color = '#DCDCDC';
            $events[] = $event;
        }

        $user_booked_times = LessonProgress::where('instructor_id', $instructor_id)->where('user_id', '=', \Auth::user()->id)->get();
        foreach($user_booked_times as $user_booked_time) {
            $event = new \stdClass();
            $event->start = str_replace(' ', 'T', $user_booked_time->start_time);
            $event->end = str_replace(' ', 'T', $user_booked_time->end_time);
            $event->display = 'background';
            $event->color = '#ff786b';
            $events[] = $event;
        }

        return response()->json($events);
    }
}
