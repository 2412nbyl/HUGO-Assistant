@extends('layout')

@section('content')
    <h2 style="color:#222;">Freelancer Dashboard</h2>
    <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    <p>Your access is limited; you cannot add or delete users.</p>
@endsection