<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with('user')
            ->where(function ($q) {
                $q->whereNull('id_user')
                    ->orWhereHas('user');
            })
            ->latest()
            ->paginate(10);

        return view('staff.index', compact('staffs'));
    }

    public function create()
    {
        $users = User::active()->get();
        $usersForStaff = $this->usersForStaffMap($users);
        return view('staff.create', compact('users', 'usersForStaff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user'     => 'nullable|string|exists:users,id',
            'name'        => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'work_status' => 'required|string',
            'phone'       => 'nullable|string',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
            'birth_date'  => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        $staff = Staff::create($validated);
        \App\Models\AuditTrail::log('staffs', $staff->id_staff, 'created', null, $staff->toArray());

        return redirect()->route('staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        $users = User::active()->get();
        $usersForStaff = $this->usersForStaffMap($users);
        return view('staff.edit', compact('staff', 'users', 'usersForStaff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'id_user'     => 'nullable|string|exists:users,id',
            'name'        => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'work_status' => 'required|string',
            'phone'       => 'nullable|string',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
            'birth_date'  => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        $old = $staff->toArray();
        $staff->update($validated);
        \App\Models\AuditTrail::log('staffs', $staff->id_staff, 'updated', $old, $staff->fresh()->toArray());

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        \App\Models\AuditTrail::log('staffs', $staff->id_staff, 'deleted', $staff->toArray(), null);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }

    /** @return array<string, array{name: string, email: string, role: string, position: string}> */
    private function usersForStaffMap($users): array
    {
        $rolePositions = [
            'admin'      => 'Administrator',
            'notaris'    => 'Notaris',
            'staff'      => 'Staff Operasional',
            'freelancer' => 'Freelancer',
        ];

        $map = [];
        foreach ($users as $user) {
            $map[$user->id] = [
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'position' => $rolePositions[$user->role] ?? ucfirst($user->role),
            ];
        }

        return $map;
    }
}
