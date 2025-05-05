<?php

namespace App\Services;

use App\Interfaces\CardServiceInterface;
use App\Models\Card;
use App\Models\Board;
use App\Models\Column;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardService implements CardServiceInterface
{
    public function getAllCards()
    {
        // TODO: Implementar lógica
    }

    public function getCardById($id)
    {
        // TODO: Implementar lógica
    }

    public function createCard(array $data, $columnId, $boardId)
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Usuario no autenticado');
        }

        $board = Board::with(['users', 'columns'])->find($boardId);

        if (!$board) {
            throw new NotFoundException('Tablero no encontrado');
        }

        $this->checkBoardPermissions($board, $user);

        $column = $board->columns()->find($columnId);

        if (!$column) {
            throw new NotFoundException('Columna no encontrada');
        }

        if (!$column->cards()->exists()) {
            $order = 1;
        } else {
            $order = $column->cards()->max('order') + 1;
        }

        $card = Card::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'column_id' => $columnId,
            'board_id' => $boardId,
            'order' => $order,
            'created_by' => $user->id
        ]);
        
        return $card->load('createdBy');
    }

    public function updateCard($id, array $data)
    {
        // TODO: Implementar lógica
    }

    public function deleteCard($id)
    {
        // TODO: Implementar lógica
    }

    private function checkBoardPermissions($board, $user)
    {
        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'editor' || 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para acceder a este tablero');
        }
    }
    
} 