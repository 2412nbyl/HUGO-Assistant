@extends('layout')

@section('content')
    <h2 style="color:#222;">Notaris Dashboard</h2>
    <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    <p>You can manage users from the navigation above.</p>
@endsection