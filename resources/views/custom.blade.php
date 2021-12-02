@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="starts_at">Date & Time (YYYY-MM-DD hour:min)</label>
            <input id="starts_at" name="starts_at" type="text">
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>
@endsection
