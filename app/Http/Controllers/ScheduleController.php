<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\LessonProgress;

class ScheduleController extends Controller
{
    public function instructorScheduleList()
    {
        $instructor_id = Instructor::where('user_id', \Auth::user()->id)->first()->id;
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
        return view('instructor.schedule')->with('events', json_encode($events));
    }
}
