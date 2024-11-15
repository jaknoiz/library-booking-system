<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    // แสดงห้องทั้งหมด
    public function index()
    {
        $rooms = Room::all();
        
        // ตรวจสอบสถานะการจองของแต่ละห้องและส่งข้อมูลเพิ่มเติมไปยัง view
        $rooms->each(function ($room) {
            // ตรวจสอบว่าห้องนี้มีการจองอยู่ในขณะนี้หรือไม่
            $currentDateTime = Carbon::now();
            $isBooked = Booking::where('room_id', $room->id)
                ->where('status', 'approved')
                ->where('start_time', '<=', $currentDateTime)
                ->where('end_time', '>=', $currentDateTime)
                ->exists();

            $room->is_booked = $isBooked;
        });

        return view('rooms.index', compact('rooms'));
    }

    // เพิ่มห้องใหม่
    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'capacity' => 'required|integer',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room added successfully.');
    }
}

