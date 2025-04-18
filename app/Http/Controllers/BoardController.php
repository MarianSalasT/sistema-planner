<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use App\Interfaces\BoardServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoardController extends Controller
{
    protected $boardService;

    public function __construct(BoardServiceInterface $boardService)
    {
        $this->boardService = $boardService;
    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            $board = $this->boardService->createBoard($request->all());

            return response()->json([
                'data' => $board,
                'message' => 'Tablero creado exitosamente',
                'status' => 201
            ], 201);
            
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'No autenticado',
                'status' => 401,
                'errors' => ['token' => $e->getMessage()]
            ], 401);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function show(Board $board)
    {
    }

    public function update(Request $request, Board $board)
    {
    }

    public function destroy(Board $board)
    {
    }
} 