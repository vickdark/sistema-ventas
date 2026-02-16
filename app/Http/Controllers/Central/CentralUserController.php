<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\CentralUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class CentralUserController extends Controller
{
    /**
     * Display a listing of the central users.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $users = CentralUser::all();
            return response()->json(['data' => $users]);
        }

        $users = CentralUser::all();
        return view('central.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new central user.
     */
    public function create()
    {
        return view('central.users.create');
    }

    /**
     * Store a newly created central user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.CentralUser::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $centralUser = CentralUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($centralUser));

        return redirect()->route('central.users.index')->with('status', 'Usuario central creado exitosamente. Se ha enviado un correo de verificación.');
    }

    /**
     * Show the form for editing the specified central user.
     */
    public function edit(CentralUser $centralUser)
    {
        return view('central.users.edit', compact('centralUser'));
    }

    /**
     * Update the specified central user in storage.
     */
    public function update(Request $request, CentralUser $centralUser)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.CentralUser::class.',email,'.$centralUser->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $centralUser->name = $request->name;
        $centralUser->email = $request->email;
        if ($request->filled('password')) {
            $centralUser->password = Hash::make($request->password);
        }
        $centralUser->save();

        return redirect()->route('central.users.index')->with('status', 'Usuario central actualizado exitosamente.');
    }

    /**
     * Remove the specified central user from storage.
     */
    public function destroy(CentralUser $centralUser)
    {
        $centralUser->delete();

        return redirect()->route('central.users.index')->with('status', 'Usuario central eliminado exitosamente.');
    }

    /**
     * Resend the email verification notification for a specific central user.
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:central.users,id'],
        ]);

        $user = CentralUser::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'El correo electrónico de este usuario ya ha sido verificado.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado exitosamente.']);
    }
}
