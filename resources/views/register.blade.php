@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <div class="form-group">
            <label for="username">RSC Username</label>
            <input type="text" name="username" id="username">
        </div>
        <div class="form-group">
            <label for="password">RSC Password</label>
            <input type="password" name="password" id="password">
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>
@endsection
