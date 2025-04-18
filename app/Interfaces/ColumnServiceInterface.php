<?php

namespace App\Interfaces;

interface ColumnServiceInterface
{
    public function getAllColumns();
    public function getColumnById($id);
    public function createColumn(array $data);
    public function updateColumn($id, array $data);
    public function deleteColumn($id);
} 