<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Appointment;
use App\Models\MeasurementProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stylist_registration_creates_a_profile_and_api_token(): void
    {
        $response = $this->postJson('/api/register', [
            'role' => 'stylist',
            'name' => 'Afi',
            'email' => 'afi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.role', 'stylist')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('stylist_profiles', [
            'user_id' => $response->json('user.id'),
        ]);
    }

    public function test_registration_requires_an_email_address_or_phone_number(): void
    {
        $this->postJson('/api/register', [
            'role' => 'client',
            'name' => 'Nadia',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'phone']);
    }

    public function test_client_can_register_with_a_phone_number_without_an_email(): void
    {
        $this->postJson('/api/register', [
            'role' => 'client',
            'name' => 'Nadia',
            'phone' => '+22997001122',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $this->assertDatabaseHas('users', [
            'phone' => '+22997001122',
            'email' => null,
        ]);
    }

    public function test_admin_can_update_a_stylist_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $stylist = User::factory()->create(['role' => 'stylist']);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/stylists/{$stylist->id}", [
            'published' => true,
        ])->assertOk();

        $this->assertDatabaseHas('stylist_profiles', [
            'user_id' => $stylist->id,
            'published' => true,
        ]);
    }

    public function test_stylist_cannot_create_an_order_with_another_stylists_product(): void
    {
        $stylist = User::factory()->create(['role' => 'stylist']);
        $client = User::factory()->create(['role' => 'client']);
        $otherStylist = User::factory()->create(['role' => 'stylist']);
        $product = Product::create([
            'stylist_id' => $otherStylist->id,
            'name' => 'Robe',
            'category' => 'sur_mesure',
            'price' => 100,
        ]);

        Sanctum::actingAs($stylist);

        $this->postJson('/api/stylist/orders', [
            'client_id' => $client->id,
            'product_id' => $product->id,
            'sale_price' => 100,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_client_can_clear_measurements_without_causing_a_server_error(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $profile = MeasurementProfile::create([
            'client_id' => $client->id,
            'label' => 'Principal',
        ]);

        Sanctum::actingAs($client);

        $this->patchJson("/api/client/measurement-profiles/{$profile->id}", [
            'measurements' => null,
        ])->assertOk();
    }

    public function test_order_update_rejects_a_null_sale_price(): void
    {
        $stylist = User::factory()->create(['role' => 'stylist']);
        $client = User::factory()->create(['role' => 'client']);
        $order = Order::create([
            'stylist_id' => $stylist->id,
            'client_id' => $client->id,
            'sale_price' => 100,
        ]);

        Sanctum::actingAs($stylist);

        $this->patchJson("/api/stylist/orders/{$order->id}", [
            'sale_price' => null,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('sale_price');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'sale_price' => 100,
        ]);
    }

    public function test_appointment_cannot_keep_an_order_for_a_different_client(): void
    {
        $stylist = User::factory()->create(['role' => 'stylist']);
        $client = User::factory()->create(['role' => 'client']);
        $otherClient = User::factory()->create(['role' => 'client']);
        $order = Order::create([
            'stylist_id' => $stylist->id,
            'client_id' => $client->id,
            'sale_price' => 100,
        ]);
        $appointment = Appointment::create([
            'stylist_id' => $stylist->id,
            'client_id' => $client->id,
            'order_id' => $order->id,
            'type' => 'Essayage',
            'starts_at' => now(),
        ]);

        Sanctum::actingAs($stylist);

        $this->patchJson("/api/stylist/appointments/{$appointment->id}", [
            'client_id' => $otherClient->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('order_id');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'client_id' => $client->id,
        ]);
    }
}
