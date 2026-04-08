<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }
}
