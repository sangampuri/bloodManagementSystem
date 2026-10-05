@extends('layouts.public')
@section('title', 'Page expired')
@section('content')
    <x-error-page code="419" title="This page has expired."
        text="You stayed on the form for a long time. Go back, refresh the page, and send it again." />
@endsection