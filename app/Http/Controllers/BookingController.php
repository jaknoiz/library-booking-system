<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // แสดงการจองทั้งหมด
    public function index()
    {
        // ตรวจสอบ role ของผู้ใช้ที่เข้าสู่ระบบ
        if (auth()->user()->role == 'admin') {
            // หากเป็น Admin ให้ดึงข้อมูลการจองทั้งหมด
            $bookings = Booking::with('room')->get();
        } else {
            // หากเป็น Member ให้ดึงเฉพาะการจองของผู้ใช้นั้นๆ
            $bookings = Booking::with('room')->where('user_id', auth()->id())->get();
        }

        return view('bookings.index', compact('bookings'));
    }

    // สร้างการจองใหม่
    public function store(Request $request)
{
    $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
    ]);

    // ตรวจสอบว่ามีการจองในสถานะ approved ในช่วงเวลาที่เลือกหรือไม่
    $conflict = Booking::where('room_id', $request->room_id)
        ->where('status', 'approved')
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($query) use ($request) {
                      $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                  });
        })
        ->exists();

    if ($conflict) {
        return back()->withErrors(['room_id' => 'This room is already approved for booking during the selected time.']);
    }

    // บันทึกการจองใหม่
    Booking::create([
        'user_id' => auth()->id(),
        'room_id' => $request->room_id,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'status' => 'waiting',
    ]);

    return redirect()->route('bookings.index')->with('success', 'Booking request submitted.');
}

    

    // อนุมัติการจอง
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        $room = $booking->room;
    
        // ปิดห้องในวันนั้น (ห้ามจองซ้ำในวันเดียวกัน)
        $room->closed = true;
        $room->save();
    
        // อัปเดตสถานะการจองเป็น approved
        $booking->status = 'approved';
        $booking->save();
    
        return redirect()->route('bookings.index')->with('status', 'Booking approved and room closed for the day');
    }
    

    // ปฏิเสธการจอง
    public function reject(Booking $booking)
    {
        $booking->status = 'rejected';
        $booking->save();
        return redirect()->route('bookings.index')->with('success', 'Booking rejected.');
    }

    public function updateRoomAvailability()
{
    // หาการจองที่สิ้นสุดแล้ว
    $bookings = Booking::where('end_time', '<', now())
                        ->where('status', 'approved')
                        ->get();

    foreach ($bookings as $booking) {
        // เปิดห้องอีกครั้ง
        $booking->room->update([
            'closed' => false
        ]);
    }
}

    
}
