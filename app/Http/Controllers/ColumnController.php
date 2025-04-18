<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;
use App\Interfaces\ColumnServiceInterface;

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

    public function store(Request $request)
    {
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