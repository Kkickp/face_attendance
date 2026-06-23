<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Auth::user()->notifications()->paginate(20);
        Auth::user()->unreadNotifications->markAsRead();
        return view('notifikasi.index', compact('notifikasi'));
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi sudah ditandai dibaca.');
    }

    public function destroy(string $id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        return back();
    }

    public function unreadCount()
    {
        return response()->json(['count' => Auth::user()->unreadNotifications()->count()]);
    }
}
