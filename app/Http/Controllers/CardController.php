<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Interfaces\CardServiceInterface;

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

    public function store(Request $request)
    {
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