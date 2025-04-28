<?php
use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create a board', function () {
    // Preparar el entorno
    $user = User::factory()->create();
    $boardData = [
        'title' => 'Test Board',
        'description' => 'Test Description',
    ];

    // Simular la accion
    $response = $this->actingAs($user)->post(route('boards.store'), $boardData);

    // Verificar la respuesta. Assert
    $response->assertStatus(201);

    // Verificar la persistencia en la base de datos.
    $this->assertDatabaseHas('boards', [
        'title' => 'Test Board',
        'description' => 'Test Description',
        'owner_id' => $user->id,
    ]);

    // Verificar la auditoria.
    // TO DO: Implementar la auditoria.
});

test('user not logged in can not create a board', function () {
    $boardData = [
        'title' => 'Test Board 2',
        'description' => 'Test Description 2',
    ];

    $response = $this->postJson(route('boards.store'), $boardData);

    $response->assertStatus(401);

    $this->assertDatabaseMissing('boards', [
        'title' => 'Test Board 2',
        'description' => 'Test Description 2',
    ]);
});

test('user can update a board being owner', function () {
    // TODO: Implementar la prueba
});

test('user can not update a board being not owner', function () {
    // TODO: Implementar la prueba
});

test('user can delete a board being owner', function () {
    // TODO: Implementar la prueba
});

test('user can not delete a board being not owner', function () {
    // TODO: Implementar la prueba
});