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

test('user can add a member to a board being owner', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);
    
    $response = $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'editor',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('board_user', [
        'board_id' => $board->id,
        'user_id' => $member->id,
        'role' => 'editor',
    ]);
});

test('user can remove a member from a board being owner', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($user)->postJson(route('boards.removeMember', $board->id), [
        'user_id' => $member->id,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseMissing('board_user', [
        'board_id' => $board->id,
        'user_id' => $member->id,
    ]);
});

test('user can not add a member to a board being not owner', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $user2->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($user2)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'editor',
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('board_user', [
        'board_id' => $board->id,
        'user_id' => $member->id,
    ]);
});

test('user can not remove a member from a board being not owner', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $user2->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($user2)->postJson(route('boards.removeMember', $board->id), [
        'user_id' => $member->id,
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('board_user', [
        'board_id' => $board->id,
        'user_id' => $member->id,
    ]);
});

test('user can update a board being owner', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->putJson(route('boards.update', $board->id), [
        'title' => 'Updated Board',
        'description' => 'Updated Description',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('boards', [
        'id' => $board->id,
        'title' => 'Updated Board',
        'description' => 'Updated Description',
        'owner_id' => $user->id,
    ]);
});

test('user can not update a board being not owner', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $response = $this->actingAs($user2)->putJson(route('boards.update', $board->id), [
        'title' => 'Updated Board Not Owner',
        'description' => 'Updated Description Not Owner',
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('boards', [
        'id' => $board->id,
        'title' => 'Updated Board Not Owner',
        'description' => 'Updated Description Not Owner',
        'owner_id' => $user->id,
    ]);
});

test('user can update a board being member and having owner role', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($member)->putJson(route('boards.update', $board->id), [
        'title' => 'Updated Board by Member',
        'description' => 'Updated Description by Member',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('boards', [
        'id' => $board->id,
        'title' => 'Updated Board by Member',
        'description' => 'Updated Description by Member',
    ]);
});

test('user can delete a board being owner', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->deleteJson(route('boards.destroy', $board->id));

    $response->assertStatus(200);

    $this->assertDatabaseMissing('boards', [
        'id' => $board->id,
    ]);
});

test('user can not delete a board being not owner', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $user2->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($user2)->deleteJson(route('boards.destroy', $board->id));

    $response->assertStatus(403);

    $this->assertDatabaseHas('boards', [
        'id' => $board->id,
    ]);
});

test('admin user can get all boards', function () {
    // Crear varios tableros con distintos propietarios
    $normalUser1 = User::factory()->create();
    $normalUser2 = User::factory()->create();
    $adminUser = User::factory()->admin()->create();

    $board1 = Board::factory()->create(['owner_id' => $normalUser1->id]);
    $board2 = Board::factory()->create(['owner_id' => $normalUser2->id]);
    
    // El admin debe poder ver todos los tableros
    $response = $this->actingAs($adminUser)->getJson(route('boards.index'));
    
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Tableros obtenidos exitosamente',
        'status' => 200,
    ]);
    // Verificar que contiene los IDs de los tableros
    $response->assertJsonPath('boards.0.id', $board1->id);
    $response->assertJsonPath('boards.1.id', $board2->id);
});

test('not admin user can not get all boards', function () {
    // Crear varios tableros con distintos propietarios
    $normalUser1 = User::factory()->create();
    $normalUser2 = User::factory()->create();
    
    Board::factory()->create(['owner_id' => $normalUser1->id]);
    Board::factory()->create(['owner_id' => $normalUser2->id]);
    
    // Un usuario normal no debería poder ver todos los tableros
    $response = $this->actingAs($normalUser1)->getJson(route('boards.index'));
    
    $response->assertStatus(403);
});

test('user can get all boards he owns or is member of', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $user->id,
    ]);

    $this->assertDatabaseHas('boards', [
        'id' => $board->id,
    ]);

    $this->actingAs($user)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($user)->getJson(route('boards.getMyBoards'));

    $response->assertStatus(200);

    $response->assertJson([
        'message' => 'Tableros obtenidos exitosamente',
        'status' => 200,
    ]);
    $response->assertJsonPath('boards.0.id', $board->id);

    $response = $this->actingAs($member)->getJson(route('boards.getMyBoards'));

    $response->assertStatus(200);

    $response->assertJson([
        'message' => 'Tableros obtenidos exitosamente',
        'status' => 200,
    ]);
    $response->assertJsonPath('boards.0.id', $board->id);
});

test('user can get a board by id being owner or member', function () {
    // Caso 1: Usuario es propietario
    $owner = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this->actingAs($owner)->getJson(route('boards.show', $board->id));
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Tablero obtenido exitosamente',
        'status' => 200,
        'board' => [
            'id' => $board->id,
            'title' => $board->title,
            'description' => $board->description,
        ],
    ]);

    // Caso 2: Usuario es miembro
    $member = User::factory()->create();
    $this->actingAs($owner)->postJson(route('boards.addMember', $board->id), [
        'user_id' => $member->id,
        'role' => 'editor',
    ]);

    $response = $this->actingAs($member)->getJson(route('boards.show', $board->id));
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Tablero obtenido exitosamente',
        'status' => 200,
        'board' => [
            'id' => $board->id,
        ],
    ]);
});

test('user can not get a board by id being not owner or member', function () {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();
    $board = Board::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $response = $this->actingAs($nonMember)->getJson(route('boards.show', $board->id));
    $response->assertStatus(403);
    $response->assertJson([
        'message' => 'No tienes permisos para acceder a este tablero',
        'status' => 403,
    ]);
});

