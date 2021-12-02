@extends('app')

@section('body')
    <form method="POST">
        @csrf
        <div class="form-group">
            <label for="category">Optional: Filter by category</label>
            <select id="category">
                <option value="—" selected>—</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="location">Select location</label>
            <select id="location" name="location">
                @foreach($locations as $location)
                    <option value="{{ $location['id'] }}"
                            data-category="{{ $location['category'] }}">{{ $location['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>
    {{--    <a href="{{ route('custom') }}">Custom registration</a>--}}
    <a href="{{ route('registrations') }}">My registrations</a>
    <a href="{{ route('custom') }}">Custom registration</a>
    <a href="{{ route('logout') }}">Logout</a>

    <script>
        const filterLocations = (event) => {
            const locationSelect = document.getElementById('location');
            const category = event.target.value;

            for (let i = 0; i < locationSelect.length; i++) {
                const option = locationSelect.options[i];
                const shouldDisplay = option.dataset.category === category || category === '—';

                option.style.display = shouldDisplay ? 'block' : 'none';
            }
        };

        const categorySelect = document.getElementById('category');
        categorySelect.addEventListener('change', filterLocations)
    </script>
@endsection
