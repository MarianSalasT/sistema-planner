<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Interfaces\CardServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardController extends Controller
{
    protected $cardService;

    public function __construct(CardServiceInterface $cardService)
    {
        $this->cardService = $cardService;
    }

    public function index()
    {
    }

    public function store(Request $request, $boardId, $columnId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $card = $this->cardService->createCard($request->all(), $columnId, $boardId);
            return response()->json([
                'card' => $card,
                'message' => 'Tarjeta creada correctamente',
                'status' => 201
            ], 201);

        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'No estás autenticado',
                'status' => 401,
                'error' => $e->getMessage()
            ], 401);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este tablero',
                'status' => 403,
                'error' => $e->getMessage()
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

    public function show(Card $card)
    {
    }

    public function update(Request $request, Card $card)
    {
    }

    public function destroy(Card $card)
    {
    }
} 