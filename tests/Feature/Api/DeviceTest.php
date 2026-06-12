<?php

it('qurilma ro\'yxatdan o\'tkazadi', function () {
    actingUser();
    $this->postJson('/api/devices', [
        'device_token' => 'abc123token',
        'platform'     => 'android',
        'device_name'  => 'Pixel',
    ])->assertCreated()->assertJsonPath('data.platform', 'android');
});

it('bir xil token ikki marta yuborilsa yangilanadi (duplicate emas)', function () {
    actingUser();
    $payload = ['device_token' => 'same-token', 'platform' => 'ios'];
    $this->postJson('/api/devices', $payload)->assertCreated();
    $this->postJson('/api/devices', $payload)->assertCreated();

    $this->assertDatabaseCount('user_devices', 1);
});

it('noto\'g\'ri platforma rad etiladi', function () {
    actingUser();
    $this->postJson('/api/devices', ['device_token' => 't', 'platform' => 'windows'])
        ->assertStatus(422);
});
