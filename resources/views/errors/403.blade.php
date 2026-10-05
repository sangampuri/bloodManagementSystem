@extends('layouts.public')
@section('title', 'No access')
@section('content')
    <x-error-page code="403" title="You don't have access to this page."
        text="This area is for a different account type. If you think this is a mistake, contact the administrator." />
@endsection