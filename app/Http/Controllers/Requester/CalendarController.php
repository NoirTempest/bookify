<?php
namespace App\Http\Controllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AssetType;

class CalendarController extends Controller
{
    public function index()
    {
        $users = User::all();
        $assetTypes = AssetType::all();

        return view('requester.calendar', compact('users', 'assetTypes'));
    }
}
