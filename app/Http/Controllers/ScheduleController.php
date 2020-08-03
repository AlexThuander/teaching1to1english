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
            $event->start = str_replace(' ', 'T', $lesson_progress->start_datetime);
            $event->end = str_replace(' ', 'T', $lesson_progress->end_datetime);
            $event->constraint = 'availableForLesson';
            $event->color = '#257e4a';
            $events[] = $event;
        }//var_dump(json_encode($events));exit;
        return view('instructor.instruction_schedule')->with('events', json_encode($events));
    }

}
