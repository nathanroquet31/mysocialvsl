<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => 'sometimes|email|unique:users,email,' . $user->id,
            'timezone'    => 'sometimes|string|timezone:all',
            'avatar_url'  => 'sometimes|nullable|url',
            'preferences' => 'sometimes|array',
        ]);

        // Merge pour ne pas écraser les préférences non envoyées
        if ($request->has('preferences')) {
            $request->merge([
                'preferences' => array_replace_recursive($user->preferences ?? [], $request->preferences),
            ]);
        }

        $user->update($request->only(['name', 'email', 'timezone', 'avatar_url', 'preferences']));

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mot de passe actuel incorrect.'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['Mot de passe incorrect.'],
            ]);
        }

        $request->user()->currentAccessToken()->delete();
        $request->user()->delete();

        return response()->json(['message' => 'Compte supprimé.']);
    }
}
