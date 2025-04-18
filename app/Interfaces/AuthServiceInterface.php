<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function register(array $data);
    public function login(array $data);
    public function logout();
    public function authenticate(Request $request);
}
