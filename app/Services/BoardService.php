<?php

namespace App\Services;

use App\Interfaces\BoardServiceInterface;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoardService implements BoardServiceInterface
{
    public function getAllBoards()
    {
        // TODO: Implementar lógica
    }

    public function getBoardById($id)
    {
        // TODO: Implementar lógica
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

        return $board->load(['owner', 'users', 'columns.cards']);
    }

    public function updateBoard($id, array $data)
    {
        // TODO: Implementar lógica
    }

    public function deleteBoard($id)
    {
        // TODO: Implementar lógica
    }
} 