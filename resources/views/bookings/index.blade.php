@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">My Bookings</h1>

        @foreach($bookings as $booking)
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">{{ $booking->room->name }} - Status: 
                        <span class="{{ $booking->status == 'approved' ? 'text-success' : ($booking->status == 'rejected' ? 'text-danger' : 'text-warning') }}">
                            {{ $booking->status }}
                        </span>
                    </h3>
                    <p><strong>Booking Time:</strong> {{ $booking->start_time }} to {{ $booking->end_time }}</p>

                    @if($booking->room->closed)
                        <p class="alert alert-danger">This room is closed for the day.</p>
                    @endif

                    @if(auth()->user()->role == 'admin')
                        <div class="d-flex gap-2">
                            <form action="{{ route('bookings.approve', $booking->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" 
                                    {{ $booking->status == 'approved' ? 'disabled' : '' }}>
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('bookings.reject', $booking->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger w-100" 
                                    {{ $booking->status == 'rejected' ? 'disabled' : '' }}>
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
