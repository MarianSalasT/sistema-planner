<?php

namespace App\Interfaces;

interface BoardServiceInterface
{
    public function getAllBoards();
    public function getBoardById($id);
    public function createBoard(array $data);
    public function updateBoard($id, array $data);
    public function deleteBoard($id);
    public function getMyBoards();
    // operations with members
    public function addMember($boardId, array $data);
    public function updateMemberRole($boardId, array $data);
    public function removeMember($boardId, $userId);
    // soft deletes
    public function restoreBoard($id);
    public function forceDeleteBoard($id);
    public function getDeletedBoards();
    public function getMyDeletedBoards();
} 