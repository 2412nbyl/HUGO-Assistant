@extends('layout')

@section('content')
    <h2>Tambah User Baru</h2>
    <form method="POST" action="/users/create">
        @csrf
        <div>
            <label>Nama:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Role:</label>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="notaris">Notaris</option>
                <option value="staff">Staff</option>
                <option value="freelancer">Freelancer</option>
            </select>
        </div>
        <button type="submit">Simpan</button>
    </form>
@endsection