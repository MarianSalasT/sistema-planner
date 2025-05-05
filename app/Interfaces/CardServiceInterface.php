<?php

namespace App\Interfaces;

interface CardServiceInterface
{
    public function getAllCards();
    public function getCardById($id);
    public function createCard(array $data, $columnId, $boardId);
    public function updateCard($id, array $data);
    public function deleteCard($id);
} 