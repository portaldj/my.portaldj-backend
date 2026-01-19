<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Create dependencies
    $country = \App\Models\Country::firstOrCreate(['name' => 'Test Country', 'code' => 'TC']);
    $city = \App\Models\City::firstOrCreate(['name' => 'Test City', 'country_id' => $country->id]);
    $djType = \App\Models\DjType::firstOrCreate(['name' => 'Club DJ']);
    // Ensure roles exist if seeding not run in test environment refresh
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'DJ']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'username' => 'testuser',
        'phone' => '1234567890',
        'country_id' => $country->id,
        'city_id' => $city->id,
        'dj_type_id' => $djType->id,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
