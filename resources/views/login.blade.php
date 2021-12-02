@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <div class="form-group">
            <label for="user">Select a user</label>
            <select name="user" id="user">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Log in</button>
        </div>
    </form>
    <a href="{{ route('register') }}">Register new user</a>
@endsection
