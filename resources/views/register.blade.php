@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <label for="username">RSC Username</label>
        <input type="text" name="username" id="username">
        <label for="password">RSC Password</label>
        <input type="password" name="password" id="password">
        <button type="submit">Register</button>
    </form>
@endsection
