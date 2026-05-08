@extends('layout')

@section('content')
    <h2 style="color:#222;">Admin Dashboard</h2>
    <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    <p>You have access to user management below.</p>
    <ul>
        <li><a href="/users/create">Tambah User</a></li>
        <li><a href="/users/manage">Hapus / Kelola User</a></li>
    </ul>
@endsection