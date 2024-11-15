@extends('layouts.app')

@section('content')
    <h1>Add New Room</h1>
    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf
        <label for="name">Room Name</label>
        <input type="text" name="name" required>

        <label for="location">Location</label>
        <input type="text" name="location" required>

        <label for="capacity">Capacity</label>
        <input type="number" name="capacity" required>

        <button type="submit">Add Room</button>
    </form>
@endsection
