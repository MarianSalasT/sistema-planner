<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class AuthService implements AuthServiceInterface
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user'
        ]);

        return $user;
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new AuthenticationException('Credenciales inválidas');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $user->tokens()->delete();
    }

    public function authenticate(Request $request)
    {
        try {
            $user = Auth::user();
        } catch (AuthenticationException $e) {
            throw new AuthenticationException('Usuario no autenticado');
        } catch (\Exception $e) {
            throw new AuthenticationException('Error al autenticar');
        }

        return $user;
    }
}
