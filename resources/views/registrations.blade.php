@extends('app')

@section('body')
    <h2>Registrations</h2>
    @foreach($registrations as $registration)
        <div>
            <span>{{ $registration['catalogusId'] }}
                - {{ \Carbon\Carbon::createFromTimestamp($registration['start'])->toDateTimeString('minute') }}</span>
        </div>
    @endforeach
    <h2>Upcoming registrations</h2>
    @foreach($upcomingRegistrations as $upcomingRegistration)
        <div>
            <span>{{ $upcomingRegistration->category }} - {{ $upcomingRegistration->starts_at->toDateTimeString('minute') }}</span>
            <a href="{{ route('registrations.delete', $upcomingRegistration) }}">Delete</a>
        </div>
    @endforeach
@endsection
