<?php

namespace App\Services;

use App\Interfaces\ColumnServiceInterface;
use App\Models\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Board;
class ColumnService implements ColumnServiceInterface
{
    public function getAllColumns()
    {
        // TODO: Implementar lógica
    }

    public function getColumnById($id)
    {
        // TODO: Implementar lógica
    }

    public function createColumn(array $data, $boardId)
    {
        $user = Auth::user();
        if (!$user) {
            throw new AuthenticationException('No estás autenticado');
        }

        $board = Board::with(['users'])->find($boardId);

        if (!$board) {
            throw new ModelNotFoundException('Tablero no encontrado');
        }
        
        $this->checkBoardMember($board, $user);
        $this->checkBoardPermissions($board, $user);

        if (!$board->columns()->exists()) {
            $order = 1;
        } else {
            $order = $board->columns()->max('order') + 1;
        }

        $column = Column::create([
            'title' => $data['title'],
            'board_id' => $boardId,
            'order' => $order,
        ]);

        return $column;
    }

    public function updateColumn($id, array $data)
    {
        // TODO: Implementar lógica
    }

    public function deleteColumn($id)
    {
        // TODO: Implementar lógica
    }

    private function checkBoardPermissions($board, $user)
    {
        if ($board->owner_id !== $user->id && !$board->users()->where('user_id', $user->id)->where('board_user.role', 'editor' || 'owner')->exists()) {
            throw new AuthorizationException('No tienes permisos para acceder a este tablero');
        }
    }

    private function checkBoardMember($board, $user)
    {
        if (!$board->users()->where('user_id', $user->id)->exists()) {
            throw new AuthorizationException('No tienes permisos para acceder a este tablero');
        }
    }
} 