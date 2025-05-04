<?php

namespace App\Services;

use App\Interfaces\BoardServiceInterface;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
class BoardService implements BoardServiceInterface
{
    public function getAllBoards()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        if ($user->role !== 'admin') {
            throw new AuthorizationException('No tienes permisos de administrador para acceder a esta información');
        }

        return Board::with(['owner', 'users', 'columns.cards'])->get();
    }

    public function getBoardById($id)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }
        
        $board = Board::with(['owner', 'users', 'columns.cards'])->find($id);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }

        if ($board->owner_id !== $user->id && !$board->users->contains($user->id)) {
            throw new AuthorizationException('No tienes permisos para acceder a este tablero');
        }

        return $board;
    }

    public function createBoard(array $data)
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'owner_id' => $user->id
        ]);

        $board->users()->attach($user->id, ['role' => 'owner']);

        return $board->load(['owner', 'users', 'columns.cards']);
    }

    public function updateBoard($id, array $data)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::with(['owner', 'users', 'columns.cards'])->find($id);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }

        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para actualizar este tablero');
        }

        $board->update($data);

        return $board->load(['owner', 'users', 'columns.cards']);
    }

    public function deleteBoard($id)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::with(['owner', 'users', 'columns.cards'])->find($id);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }

        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para eliminar este tablero');
        }

        $board->delete();

        return $board;
    }

    public function getMyBoards()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }
        
        // Obtener tableros donde el usuario es dueño (owner_id)
        $ownedBoards = Board::with(['owner', 'users', 'columns.cards'])
            ->where('owner_id', $user->id)
            ->get();
        
        // Obtener tableros donde el usuario es participante (board_user)
        $memberBoards = $user->boards()
            ->with(['owner', 'users', 'columns.cards'])
            ->get();
            
        // Combinar los resultados y eliminar duplicados
        $combinedBoards = $ownedBoards->merge($memberBoards)->unique('id');
        
        return $combinedBoards;
    }

    public function addMember($boardId, array $data)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::with(['owner', 'users', 'columns.cards'])->find($boardId);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }

        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para agregar miembros a este tablero');
        }
        
        $board->users()->attach($data['user_id'], ['role' => $data['role']]);

        return $board->load(['owner', 'users', 'columns.cards']);
    }

    public function removeMember($boardId, $userId)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::with(['owner', 'users', 'columns.cards'])->find($boardId);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }

        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para eliminar miembros de este tablero');
        }

        $board->users()->detach($userId);

        return $board->load(['owner', 'users', 'columns.cards']);
    }
} 