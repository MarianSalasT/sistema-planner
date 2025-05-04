<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use App\Interfaces\BoardServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

class BoardController extends Controller
{
    protected $boardService;

    public function __construct(BoardServiceInterface $boardService)
    {
        $this->boardService = $boardService;
    }

    public function index()
    {
        try {
            $boards = $this->boardService->getAllBoards();

            return response()->json([
                'message' => 'Tableros obtenidos exitosamente',
                'status' => 200,
                'boards' => $boards,
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tableros',
                'status' => 500,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $board = $this->boardService->getBoardById($id);
            
            return response()->json([
                'message' => 'Tablero obtenido exitosamente',
                'status' => 200,
                'board' => $board,
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
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

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Filtrar solo los campos presentes en la solicitud
            $data = array_filter($request->only(['title', 'description']), function ($value) {
                return $value !== null;
            });
            
            $board = $this->boardService->updateBoard($id, $data);

            return response()->json([
                'board' => $board,
                'message' => 'Tablero actualizado exitosamente',
                'status' => 200
            ], 200);   
            
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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $board = $this->boardService->deleteBoard($id);

            return response()->json([
                'message' => 'Tablero eliminado exitosamente',
                'status' => 200
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function getMyBoards()
    {
        try {
            $boards = $this->boardService->getMyBoards();

            return response()->json([
                'message' => 'Tableros obtenidos exitosamente',
                'status' => 200,
                'boards' => $boards,
            ], 200);

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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tableros',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function addMember(Request $request, $boardId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'role' => 'required|string|in:owner,editor,viewer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            $board = $this->boardService->addMember($boardId, $request->all());

            return response()->json([
                'data' => $board,
                'message' => 'Miembro agregado al tablero exitosamente',
                'status' => 200
            ], 200);

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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al agregar el miembro al tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function updateMemberRole(Request $request, $boardId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'role' => 'required|string|in:owner,editor,viewer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            $board = $this->boardService->updateMemberRole($boardId, $request->all());

            return response()->json([
                'data' => $board,
                'message' => 'Rol de miembro actualizado exitosamente',
                'status' => 200
            ], 200);

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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el rol de miembro',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function removeMember(Request $request, $boardId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            $board = $this->boardService->removeMember($boardId, $request->user_id);

            return response()->json([
                'message' => 'Miembro eliminado del tablero exitosamente',
                'status' => 200
            ], 200);

        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'No autenticado',
                'status' => 401,
                'errors' => ['token' => $e->getMessage()]
            ], 401);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar miembros de este tablero',
                'status' => 403,
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el miembro del tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }
    
    public function restoreBoard($id)
    {
        try {
            $board = $this->boardService->restoreBoard($id);

            return response()->json([
                'message' => 'Tablero restaurado exitosamente',
                'status' => 200,
                'board' => $board,
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar el tablero',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function forceDeleteBoard($id)
    {
        try {
            $board = $this->boardService->forceDeleteBoard($id);

            return response()->json([
                'message' => 'Tablero eliminado permanentemente',
                'status' => 200,
                'board' => $board,
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el tablero permanentemente',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function getDeletedBoards()
    {
        try {
            $boards = $this->boardService->getDeletedBoards();

            return response()->json([
                'message' => 'Tableros eliminados obtenidos exitosamente',
                'status' => 200,
                'boards' => $boards,
            ], 200);

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
                'message' => 'Tablero no encontrado',
                'status' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tableros eliminados',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }

    public function getMyDeletedBoards()
    {
        try {
            $boards = $this->boardService->getMyDeletedBoards();

            return response()->json([
                'message' => 'Tableros eliminados obtenidos exitosamente',
                'status' => 200,
                'boards' => $boards,
            ], 200);

        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Usuario no autenticado',
                'status' => 401,
                'errors' => ['token' => $e->getMessage()]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tableros eliminados',
                'status' => 500,
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }
} 