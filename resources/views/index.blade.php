@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <label for="location">Select location</label>
        <select id="location" name="location">
            @foreach($locations as $location)
                <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
            @endforeach
        </select>
        <button type="submit">Register</button>
    </form>
{{--    <a href="{{ route('custom') }}">Custom registration</a>--}}
    <a href="{{ route('logout') }}">Logout</a>
@endsection
