<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use App\Interfaces\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->authService->register($request->all());

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'status' => 201
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $credentials = $request->only('email', 'password');
            $data = $this->authService->login($credentials);
        } catch (AuthenticationException $e) {

            return response()->json([
                'message' => 'Error de autenticación',
                'error' => $e->getMessage(),
                'status' => 401
            ], 401);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al autenticar',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
        
        return response()->json([
            'message' => 'Usuario autenticado exitosamente',
            'data' => $data,
            'status' => 200
        ], 200);
    }

    public function logout()
    {
        try {
            $this->authService->logout();
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage(),
                'status' => 401
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
            'status' => 200
        ], 200);
    }

    public function authenticate(Request $request)
    {
    }
}
