<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpentokController extends Controller
{
    const LARATOK_SESSION_NAME_EXAMPLE = '1to1';

    public function instructorOpentok(Request $request)
    {
        // Check if session example already exists.
        $sessions = DB::table('laratok_sessions')
            ->select('sessionId')
            ->where('instructor_id', '=', \Auth::user()->id)
            ->get()
            ->first();

        // if it not exists, it will generate the session example.
        if ($sessions == NULL) {
            $laratok = new LaraTok();
            $session_params = array();
            $session_params['name'] = self::LARATOK_SESSION_NAME_EXAMPLE;
            $session = $laratok->generateSession(\Auth::user()->id, $session_params);
            $laratok->generateToken($request->lesson_id, $session);
        }

        // Retrieve session and token.
        $laratok = DB::table('laratok_sessions')
            ->select('sessionId')
            ->crossJoin('laratok_tokens', 'laratok_sessions.id', '=', 'laratok_tokens.session_id')
            ->select('laratok_tokens.*', 'laratok_sessions.*')
            ->where('laratok_sessions.instructor_id', \Auth::user()->id)
            ->where('laratok_tokens.lesson_progress_id', $request->lesson_id)
            ->get()
            ->first();

        return view('laratok::examples.simples', compact('laratok'));
    }
}
