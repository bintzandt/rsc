@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <label for="user">Select a user</label>
        <select name="user" id="user">
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->username }}</option>
            @endforeach
        </select>
        <a href="{{ route('register') }}">Register new user</a>
        <button type="submit">Log in</button>
    </form>
@endsection
