<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Role;
use App\Models\Instructor;
use App\Models\InstructionLevel;
use App\Models\Credit;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Image;
use SiteHelpers;
use Crypt;
use URL;
use Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactInstructor;

class InstructorController extends Controller
{
    private $this_week_sd;
    private $this_week_ed;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $monday = strtotime("last monday");        
        $sunday = strtotime(date("Y-m-d", $monday)." +6 days");        
        $this->this_week_sd = date("Y-m-d", $monday);
        $this->this_week_ed = date("Y-m-d", $sunday);
    }

    public function instructorList($timezone='GMT', Request $request)
    {
        $category_search = $request->input('category_id');
        $instruction_level_id = $request->input('instruction_level_id');
        $keyword = $request->input('keyword');
        
        $query = DB::table('instructors')
                    ->select('instructors.*')
                    ->join('users', 'users.id', '=', 'instructors.user_id')
                    ->leftJoin('instructor_filters', 'instructor_filters.instructor_id', '=', 'instructors.id')
                    ->where('users.is_active',1)
                    ->groupBy('instructors.id');
        
        //filter categories as per user selected                
        if($category_search && $category_search != 0) {
            $query->where('instructor_filters.category_id', $category_search);
        }
        //filter courses as per keyword
        if($keyword) {
            $query->where('instructors.first_name', 'LIKE', '%' . $keyword . '%');
            $query->orWhere('instructors.last_name', 'LIKE', '%' . $keyword . '%');
        }

        //filter instruction levels as per user selected                
        if($instruction_level_id && $instruction_level_id != 0) {
            $query->where('instructor_filters.instruction_level_id', $instruction_level_id);
        }
        
        $instructors = $query->paginate(8);

        foreach($instructors as $key => $instructor) {

            $student_count = DB::table('lesson_progress')
                            ->select('lesson_progress.*')
                            ->where('lesson_progress.instructor_id',$instructor->id)
                            ->where('end_time','>','now()')
                            ->groupBy('lesson_progress.user_id')
                            ->get()
                            ->count();

            $lesson_count = DB::table('lesson_progress')
                            ->select('lesson_progress.*')
                            ->where('lesson_progress.instructor_id',$instructor->id)
                            ->where('end_time','>','now()')
                            ->get()
                            ->count();

            $available_timespans = array();

            $monday = strtotime("last monday");
            $sunday = strtotime(date("Y-m-d", $monday)." +6 days");

            $available_timespan = array();
            for($i = $monday; $i <= $sunday; $i+=24*3600) {
                $tarday = date('Y-m-d', $i);                
                $start = $tarday . ' 06:00:00'; $end = $tarday . ' 12:00:00';

                $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor->id, $start, $end, $start, $end])[0]->timespan/60;
            }
            $available_timespans['morning'] = $available_timespan;

            $available_timespan = array();
            for($i = $monday; $i <= $sunday; $i+=24*3600) {
                $tarday = date('Y-m-d', $i);                
                $start = $tarday . ' 06:00:00'; $end = $tarday . ' 12:00:00';
                
                $start = $tarday . ' 12:00:00'; $end = $tarday . ' 18:00:00';
                $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor->id, $start, $end, $start, $end])[0]->timespan/60;
            }
            $available_timespans['afternoon'] = $available_timespan;

            $available_timespan = array();
            for($i = $monday; $i <= $sunday; $i+=24*3600) {
                $tarday = date('Y-m-d', $i);                
                $start = $tarday . ' 06:00:00'; $end = $tarday . ' 12:00:00';
                
                $start = $tarday . ' 18:00:00'; $end = $tarday . ' 24:00:00';
                $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor->id, $start, $end, $start, $end])[0]->timespan/60;
            }
            $available_timespans['evening'] = $available_timespan;

            $available_timespan = array();
            for($i = $monday; $i <= $sunday; $i+=24*3600) {
                $tarday = date('Y-m-d', $i);                
                $start = $tarday . ' 06:00:00'; $end = $tarday . ' 12:00:00';
                
                $start = $tarday . ' 00:00:00'; $end = $tarday . ' 06:00:00';
                $available_timespan[] = DB::select("SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, IF(start_time<?,?,start_time), IF(end_time>?,?,end_time))),0) AS timespan FROM instructor_available_time WHERE instructor_id=? and (start_time>? and start_time<? or (end_time>? and end_time<?))", [$start, $start, $end, $end, $instructor->id, $start, $end, $start, $end])[0]->timespan/60;
            }
            $available_timespans['night'] = $available_timespan;
            
            //var_dump($available_timespans);exit;

            $instructors[$key]->student_count = $student_count;
            $instructors[$key]->lesson_count = $lesson_count;
            $instructors[$key]->timespans = $available_timespans;
        }

        return view('site/home', compact('instructors', 'category_search', 'instruction_level_id'));       
    }

    public function instructorView($instructor_slug = '', Request $request)
    {
        $instructor = Instructor::where('instructor_slug', $instructor_slug)->first();
        $metrics = Instructor::metrics($instructor->id);
        return view('site.instructor_view', compact('instructor', 'metrics'));
    }

    public function dashboard(Request $request)
    {
        $instructor_id = \Auth::user()->instructor->id;
        $courses = DB::table('courses')
                        ->select('courses.*', 'categories.name as category_name')
                        ->leftJoin('categories', 'categories.id', '=', 'courses.category_id')
                        ->where('courses.instructor_id', $instructor_id)
                        ->paginate(5);
        $metrics = Instructor::metrics($instructor_id);
        return view('instructor.dashboard', compact('courses', 'metrics'));
    }

    public function contactInstructor(Request $request)
    {
        $instructor_email = $request->instructor_email;
        Mail::to($instructor_email)->send(new ContactInstructor($request));
        return $this->return_output('flash', 'success', 'Thanks for your message, will contact you shortly', 'back', '200');
    }
    public function becomeInstructor(Request $request)
    {
        if(!\Auth::check()){
            return $this->return_output('flash', 'error', 'Please login to become an Instructor', 'back', '422');
        }

        $instructor = new Instructor();

        $instructor->user_id = \Auth::user()->id;
        $instructor->first_name = $request->input('first_name');
        $instructor->last_name = $request->input('last_name');
        $instructor->contact_email = $request->input('contact_email');

        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');

        //create slug only while add
        $slug = $first_name.'-'.$last_name;
        $slug = str_slug($slug, '-');

        $results = DB::select(DB::raw("SELECT count(*) as total from instructors where instructor_slug REGEXP '^{$slug}(-[0-9]+)?$' "));

        $finalSlug = ($results['0']->total > 0) ? "{$slug}-{$results['0']->total}" : $slug;
        $instructor->instructor_slug = $finalSlug;

        $instructor->telephone = $request->input('telephone');
        $instructor->paypal_id = $request->input('paypal_id');
        $instructor->biography = $request->input('biography');
        $instructor->save();

        $user = User::find(\Auth::user()->id);

        $role = Role::where('name', 'instructor')->first();
        $user->roles()->attach($role);
        
        return redirect()->route('instructor.dashboard') ;
    }

    public function getProfile(Request $request)
    {
        $instructor = Instructor::where('user_id', \Auth::user()->id)->first();
        // echo '<pre>';print_r($instructor);exit;
        return view('instructor.profile', compact('instructor'));
    }

    public function saveProfile(Request $request)
    {
        // echo '<pre>';print_r($_FILES);exit;
        $validation_rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_email' => 'required|string|email|max:255',
            'telephone' => 'required|string|max:255',
            'paypal_id' => 'required|string|email|max:255',
            'biography' => 'required',            
        ];

        $validator = Validator::make($request->all(),$validation_rules);

        // Stop if validation fails
        if ($validator->fails()) {
            return $this->return_output('error', 'error', $validator, 'back', '422');
        }

        $instructor = Instructor::where('user_id', \Auth::user()->id)->first();
        $instructor->first_name = $request->input('first_name');
        $instructor->last_name = $request->input('last_name');
        $instructor->contact_email = $request->input('contact_email');

        $instructor->telephone = $request->input('telephone');
        $instructor->mobile = $request->input('mobile');

        $instructor->link_facebook = $request->input('link_facebook');
        $instructor->link_linkedin = $request->input('link_linkedin');
        $instructor->link_twitter  = $request->input('link_twitter');
        $instructor->link_googleplus = $request->input('link_googleplus');

        $instructor->paypal_id = $request->input('paypal_id');
        $instructor->biography = $request->input('biography');


        if (Input::hasFile('course_image') && Input::has('course_image_base64')) {
            //delete old file
            $old_image = $request->input('old_course_image');
            if (Storage::exists($old_image)) {
                Storage::delete($old_image);
            }

            //get filename
            $file_name   = $request->file('course_image')->getClientOriginalName();

            // returns Intervention\Image\Image
            $image_make = Image::make($request->input('course_image_base64'))->encode('jpg');

            // create path
            $path = "instructor/".$instructor->id;
            
            //check if the file name is already exists
            $new_file_name = SiteHelpers::checkFileName($path, $file_name);

            //save the image using storage
            Storage::put($path."/".$new_file_name, $image_make->__toString(), 'public');

            $instructor->instructor_image = $path."/".$new_file_name;
            
        }

        $instructor->save();

        return $this->return_output('flash', 'success', 'Profile updated successfully', 'instructor-profile', '200');

    }

    public function credits(Request $request)
    {
        $credits = Credit::where('instructor_id', \Auth::user()->instructor->id)
                        ->where('credits_for', 1)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('instructor.credits', compact('credits'));
    }

    public function withdrawRequest(Request $request)
    {
        $withdraw_request = new WithdrawRequest();

        $withdraw_request->instructor_id = \Auth::user()->instructor->id;
        $withdraw_request->paypal_id = $request->input('paypal_id');
        $withdraw_request->amount = $request->input('amount');
        $withdraw_request->save();

        return $this->return_output('flash', 'success', 'Withdraw requested successfully', 'instructor-credits', '200');
    }

    public function listWithdrawRequests(Request $request)
    {
        $withdraw_requests = WithdrawRequest::where('instructor_id', \Auth::user()->instructor->id)
                            ->paginate(10);

        return view('instructor.withdraw_requests', compact('withdraw_requests'));
    }
    
}
