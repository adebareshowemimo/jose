<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompleteSignupController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->role_id) {
            return $this->redirectByRole($request->user());
        }

        return view('pages.auth.complete-signup', [
            'pageTitle' => 'Finish setting up your account',
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'role' => 'required|in:candidate,employer',
            'company_name' => 'required_if:role,employer|string|max:255',
        ], [
            'role.required' => 'Please choose an account type.',
            'company_name.required_if' => 'Company name is required for employer accounts.',
        ]);

        $role = Role::where('name', $request->input('role'))->first();
        if (! $role) {
            return back()->withErrors(['role' => 'Invalid account type selected.'])->withInput();
        }

        $user = $request->user();
        $user->role_id = $role->id;
        $user->save();

        if ($request->input('role') === 'employer' && ! $user->company) {
            $user->company()->create([
                'name' => $request->input('company_name'),
                'slug' => Str::slug($request->input('company_name')) . '-' . $user->id,
                'email' => $user->email,
                'status' => 'active',
            ]);
        }

        return $this->redirectByRole($user);
    }

    protected function redirectByRole($user)
    {
        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role_id === 2) {
            return redirect()->route('employer.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
}
