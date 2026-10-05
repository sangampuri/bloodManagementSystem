@extends('layouts.public')
@section('title', 'Page not found')
@section('content')
    <x-error-page code="404" title="We couldn't find that page."
        text="The link may be old, or the record was removed." />
@endsection