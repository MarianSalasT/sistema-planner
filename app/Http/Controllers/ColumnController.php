<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;
use App\Interfaces\ColumnServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ColumnController extends Controller
{
    protected $columnService;

    public function __construct(ColumnServiceInterface $columnService)
    {
        $this->columnService = $columnService;
    }

    public function index()
    {
    }

    public function store(Request $request, $boardId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $column = $this->columnService->createColumn($request->all(), $boardId);
            return response()->json([
                'column' => $column,
                'message' => 'Columna creada exitosamente',
                'status' => 201
            ], 201);

        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'No autenticado',
                'status' => 401,
                'errors' => ['token' => $e->getMessage()]
            ], 401);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 403,
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 404,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        } 
    }

    public function show(Column $column)
    {
    }

    public function update(Request $request, Column $column)
    {
    }

    public function destroy(Column $column)
    {
    }
} 