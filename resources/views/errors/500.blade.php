@extends('layouts.public')
@section('title', 'Something went wrong')
@section('content')
    <x-error-page code="500" title="Something went wrong on our side."
        text="Try again in a moment. If it keeps happening, tell the administrator." />
@endsection