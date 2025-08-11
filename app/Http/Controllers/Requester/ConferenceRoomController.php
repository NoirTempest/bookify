<?php

namespace App\Http\Controllers\Requester;

use App\Http\Controllers\Controller;

class ConferenceRoomController extends Controller
{
    public function index()
    {
        return view('requester.conference-room');
    }
}

