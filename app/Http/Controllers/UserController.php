<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
 
class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user()->load('roles');

        return view('frontend.profile', compact('user'));
    }
public function index()
{
    $query = User::with('roles');

    if (!Auth::user()->hasRole('super')) {
        $query->where('created_by', Auth::id());
    }

    // Removed paginate(), now fetch all records
    $users = $query->get();

    return view('frontend.user-management.users.index', compact('users'));
}
    public function create()
    {
        $currentUser = Auth::user();
    
        if ($currentUser->hasRole('super')) {
            $roles = Role::where('name', '!=', 'super')->get();
        } else {
            $roles = Role::whereNotIn('name', ['super','admin'])->get();
        }
    
        $user   = new User();
        $isEdit = false;
    
        return view('frontend.user-management.users.create', compact('roles','user','isEdit'));
    }
    

    public function toggleStatus(User $user)
    {
        if (!Auth::user()->hasRole('super')) {
            abort(403, 'Accesso non autorizzato.');
        }
    
        $user->status = !$user->status;
        $user->save();
    
        $relatedUsers = User::where('created_by', $user->id)->get();
        foreach ($relatedUsers as $relatedUser) {
            $relatedUser->status = $user->status;
            $relatedUser->save();
        }
    
        return redirect()->back()
            ->with('success', 'Stato dell’utente aggiornato.');
    }
    
    public function edit(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('super')) {
            $roles = Role::all();
        } else {
            $roles = Role::whereNotIn('name', ['super', 'admin'])->get();
        }

        $isEdit = true;

        return view('frontend.user-management.users.create', compact('roles', 'user', 'isEdit'));
    }

public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'role'            => 'required|exists:roles,id',
            'expiry_enabled'  => 'nullable|in:on',
            'expiry_date'     => 'required_if:expiry_enabled,on|date|after_or_equal:today',
        ]);

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'created_by'   => Role::findOrFail($data['role'])->name === 'admin' ? null : auth()->id(),
            'expiry_date'  => $request->has('expiry_enabled')
                              ? $data['expiry_date']
                              : null,
        ]);

        $user->syncRoles(Role::findOrFail($data['role']));

        return redirect()
            ->route('users.index')
            ->with('success', 'Utente creato con successo!');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|string|min:6',
            'role'            => 'required|exists:roles,id',
            'expiry_enabled'  => 'nullable|in:on',
            'expiry_date'     => 'required_if:expiry_enabled,on|date|after_or_equal:today',
        ]);

        $user->name       = $data['name'];
        $user->email      = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->expiry_date = $request->has('expiry_enabled')
                              ? $data['expiry_date']
                              : null;

        $user->save();

        $user->syncRoles(Role::findOrFail($data['role']));

        return redirect()
            ->route('users.index')
            ->with('success', 'Utente aggiornato con successo!');
    }

    public function show(User $user)
    {
        // You may add a check here if needed
        return view('frontend.user-management.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Utente eliminato.');
    }
}
