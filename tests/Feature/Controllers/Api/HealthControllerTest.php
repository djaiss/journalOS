<?php

declare(strict_types=1);

it('checks the health of the application', function (): void {
    $response = $this->json('GET', '/api/health');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'ok',
        'status' => 200,
    ]);
});
