@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Available Rooms</h1>
        <div class="row">
            @foreach($rooms as $room)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">{{ $room->name }}</h3>
                            <p><strong>Location:</strong> {{ $room->location }}</p>
                            <p><strong>Capacity:</strong> {{ $room->capacity }} people</p>

                            @if($room->closed)
                                <p class="alert alert-danger">This room is closed for the day.</p>
                            @elseif(!$room->is_booked)
                                <form action="{{ route('bookings.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    
                                    <div class="form-group">
                                        <label for="start_time">Start Time</label>
                                        <input type="datetime-local" name="start_time" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="end_time">End Time</label>
                                        <input type="datetime-local" name="end_time" class="form-control" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Book Room</button>
                                </form>
                            @else
                                <p class="alert alert-warning">Status: Booked</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
