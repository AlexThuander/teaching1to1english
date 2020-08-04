<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\LessonProgress;
use App\Models\InstructorAvailableTime;

class ScheduleController extends Controller
{
    public function instructorScheduleList()
    {
        return view('instructor.instructor_schedule');
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
