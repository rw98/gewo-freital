<?php

use App\Models\User;

it('displays the employee listing requests index', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/verwaltung/anfragen');

    $response->assertSuccessful();
});
