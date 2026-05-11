<?php

namespace Tests\Feature\Api;

use App\Http\Resources\Api\BookingTransactionApiResource;
use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingTransactionApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resource_returns_all_model_attributes(): void
    {
        $bookingTransaction = BookingTransaction::factory()
            ->has(TransactionDetail::factory()->count(2), 'details')
            ->create();

        $resource = new BookingTransactionApiResource($bookingTransaction);
        $request = Request::create('/api/booking-transaction', 'POST');
        $response = $resource->response($request)->getData(true);

        $data = $response['data'] ?? $response;

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('booking_trx_id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('phone', $data);
        $this->assertArrayHasKey('proof', $data);
        $this->assertArrayHasKey('post_code', $data);
        $this->assertArrayHasKey('city', $data);
        $this->assertArrayHasKey('address', $data);
        $this->assertArrayHasKey('sub_total_amount', $data);
        $this->assertArrayHasKey('total_amount', $data);
        $this->assertArrayHasKey('total_tax_amount', $data);
        $this->assertArrayHasKey('total_qty', $data);
        $this->assertArrayHasKey('is_paid', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);

        $this->assertEquals($bookingTransaction->id, $data['id']);
        $this->assertEquals($bookingTransaction->booking_trx_id, $data['booking_trx_id']);
        $this->assertEquals($bookingTransaction->name, $data['name']);
        $this->assertEquals($bookingTransaction->email, $data['email']);
        $this->assertEquals($bookingTransaction->sub_total_amount, $data['sub_total_amount']);
        $this->assertEquals($bookingTransaction->total_amount, $data['total_amount']);
        $this->assertEquals($bookingTransaction->total_tax_amount, $data['total_tax_amount']);
        $this->assertEquals($bookingTransaction->is_paid, $data['is_paid']);
    }

    public function test_resource_includes_loaded_details_relationship(): void
    {
        $bookingTransaction = BookingTransaction::factory()
            ->has(TransactionDetail::factory()->count(3), 'details')
            ->create();

        $resource = new BookingTransactionApiResource($bookingTransaction->load('details'));
        $request = Request::create('/api/booking-transaction', 'POST');
        $response = $resource->response($request)->getData(true);

        $data = $response['data'] ?? $response;

        $this->assertArrayHasKey('details', $data);
        $this->assertCount(3, $data['details']);

        foreach ($data['details'] as $detail) {
            $this->assertArrayHasKey('id', $detail);
            $this->assertArrayHasKey('price', $detail);
            $this->assertArrayHasKey('qty', $detail);
            $this->assertArrayHasKey('cosmetic_id', $detail);
            $this->assertArrayHasKey('booking_transaction_id', $detail);
        }
    }

    public function test_resource_returns_correct_boolean_for_is_paid(): void
    {
        $paidTransaction = BookingTransaction::factory()->paid()->create();
        $unpaidTransaction = BookingTransaction::factory()->unpaid()->create();

        $request = Request::create('/api/booking-transaction', 'POST');

        $paidResource = new BookingTransactionApiResource($paidTransaction);
        $paidResponse = $paidResource->response($request)->getData(true);

        $unpaidResource = new BookingTransactionApiResource($unpaidTransaction);
        $unpaidResponse = $unpaidResource->response($request)->getData(true);

        $this->assertTrue(($paidResponse['data'] ?? $paidResponse)['is_paid']);
        $this->assertFalse(($unpaidResponse['data'] ?? $unpaidResponse)['is_paid']);
    }

    public function test_can_create_booking_transaction_via_api(): void
    {
        Storage::fake('public');

        $cosmetics = Cosmetic::factory()->count(2)->create();

        $payload = [
            'name' => 'John Doe',
            'phone' => '08123456789',
            'email' => 'john@example.com',
            'city' => 'Jakarta',
            'address' => 'Jl. Contoh No. 123',
            'post_code' => '12345',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => $cosmetics[0]->id, 'qty' => 2],
                ['id' => $cosmetics[1]->id, 'qty' => 1],
            ],
        ];

        $response = $this->post('/api/booking-transaction', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'booking_trx_id',
                    'name',
                    'email',
                    'phone',
                    'city',
                    'address',
                    'post_code',
                    'sub_total_amount',
                    'total_amount',
                    'total_tax_amount',
                    'total_qty',
                    'is_paid',
                    'details' => [
                        '*' => ['id', 'price', 'qty', 'cosmetic_id', 'booking_transaction_id'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('booking_transactions', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'city' => 'Jakarta',
            'address' => 'Jl. Contoh No. 123',
            'post_code' => '12345',
            'is_paid' => false,
        ]);

        $bookingTrxId = $response->json('data.booking_trx_id');
        $bookingTransaction = BookingTransaction::where('booking_trx_id', $bookingTrxId)->first();
        $this->assertNotNull($bookingTransaction);
        $this->assertCount(2, $bookingTransaction->details);
    }

    public function test_store_calculates_totals_correctly(): void
    {
        Storage::fake('public');

        $cosmetic1 = Cosmetic::factory()->create(['price' => 100000]);
        $cosmetic2 = Cosmetic::factory()->create(['price' => 200000]);

        $payload = [
            'name' => 'Jane Doe',
            'phone' => '08123456788',
            'email' => 'jane@example.com',
            'city' => 'Bandung',
            'address' => 'Jl. Contoh No. 456',
            'post_code' => '54321',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => $cosmetic1->id, 'qty' => 3],
                ['id' => $cosmetic2->id, 'qty' => 2],
            ],
        ];

        $response = $this->post('/api/booking-transaction', $payload);

        $response->assertStatus(201);

        $expectedSubTotal = (100000 * 3) + (200000 * 2);
        $expectedTotalQty = 3 + 2;
        $expectedTax = (int) (0.11 * $expectedSubTotal);
        $expectedGrandTotal = $expectedSubTotal + $expectedTax;

        $response->assertJson([
            'data' => [
                'sub_total_amount' => $expectedSubTotal,
                'total_qty' => $expectedTotalQty,
                'total_tax_amount' => $expectedTax,
                'total_amount' => $expectedGrandTotal,
                'is_paid' => false,
            ],
        ]);
    }

    public function test_store_generates_unique_booking_trx_id(): void
    {
        Storage::fake('public');

        $cosmetic = Cosmetic::factory()->create(['price' => 50000]);

        $payload = [
            'name' => 'Test User',
            'phone' => '08123456787',
            'email' => 'test@example.com',
            'city' => 'Surabaya',
            'address' => 'Jl. Contoh No. 789',
            'post_code' => '67890',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => $cosmetic->id, 'qty' => 1],
            ],
        ];

        $response = $this->post('/api/booking-transaction', $payload);

        $response->assertStatus(201);
        $bookingTrxId = $response->json('data.booking_trx_id');
        $this->assertStringStartsWith('SVX-', $bookingTrxId);
        $this->assertEquals(14, strlen($bookingTrxId));
    }

    public function test_store_handles_proof_upload(): void
    {
        Storage::fake('public');

        $cosmetic = Cosmetic::factory()->create(['price' => 75000]);

        $file = UploadedFile::fake()->image('proof.jpg', 200, 200);

        $payload = [
            'name' => 'Upload User',
            'phone' => '08123456786',
            'email' => 'upload@example.com',
            'city' => 'Yogyakarta',
            'address' => 'Jl. Upload No. 1',
            'post_code' => '55555',
            'proof' => $file,
            'cosmetics' => [
                ['id' => $cosmetic->id, 'qty' => 1],
            ],
        ];

        $response = $this->post('/api/booking-transaction', $payload);

        $response->assertStatus(201);

        $proof = $response->json('data.proof');
        $this->assertNotNull($proof);

        Storage::disk('public')->assertExists($proof);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/booking-transaction', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'phone',
                'email',
                'city',
                'address',
                'post_code',
                'proof',
                'cosmetics',
            ]);
    }

    public function test_store_validates_cosmetics_structure(): void
    {
        $payload = [
            'name' => 'Test',
            'phone' => '08123456785',
            'email' => 'test@test.com',
            'city' => 'City',
            'address' => 'Address',
            'post_code' => '12345',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => 99999, 'qty' => 1],
            ],
        ];

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/booking-transaction', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cosmetics.0.id']);
    }

    public function test_store_validates_cosmetic_qty_minimum(): void
    {
        $cosmetic = Cosmetic::factory()->create();

        $payload = [
            'name' => 'Test',
            'phone' => '08123456784',
            'email' => 'test@test.com',
            'city' => 'City',
            'address' => 'Address',
            'post_code' => '12345',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => $cosmetic->id, 'qty' => 0],
            ],
        ];

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/booking-transaction', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cosmetics.0.qty']);
    }

    public function test_store_validates_proof_must_be_image(): void
    {
        $cosmetic = Cosmetic::factory()->create();

        $payload = [
            'name' => 'Test',
            'phone' => '08123456783',
            'email' => 'test@test.com',
            'city' => 'City',
            'address' => 'Address',
            'post_code' => '12345',
            'proof' => UploadedFile::fake()->create('document.pdf', 100),
            'cosmetics' => [
                ['id' => $cosmetic->id, 'qty' => 1],
            ],
        ];

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/booking-transaction', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['proof']);
    }

    public function test_cannot_create_duplicate_cosmetic_ids(): void
    {
        Storage::fake('public');

        $cosmetic = Cosmetic::factory()->create(['price' => 100000]);

        $payload = [
            'name' => 'Test',
            'phone' => '08123456782',
            'email' => 'test@test.com',
            'city' => 'City',
            'address' => 'Address',
            'post_code' => '12345',
            'proof' => UploadedFile::fake()->image('proof.jpg'),
            'cosmetics' => [
                ['id' => $cosmetic->id, 'qty' => 1],
                ['id' => $cosmetic->id, 'qty' => 2],
            ],
        ];

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/booking-transaction', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cosmetics.0.id']);
    }
}
