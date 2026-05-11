<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransaction;
use App\Http\Resources\Api\BookingTransactionApiResource;
use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingTransaction $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('proof')) {
                $path = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $path;
            }

            $totalPrice = 0;
            $totalQty = 0;

            $products = $validated['cosmetics'];

            $cosmeticIds = array_column($products, 'id');
            $cosmetics = Cosmetic::whereIn('id', $cosmeticIds)->get()->keyBy('id');

            foreach ($products as $product) {
                $cosmetic = $cosmetics->get($product['id']);
                if ($cosmetic) {
                    $totalPrice += $cosmetic->price * $product['qty'];
                    $totalQty += $product['qty'];
                }
            }

            $tax = 0.11 * $totalPrice;
            $grandTotal = $totalPrice + $tax;

            $validated['sub_total_amount'] = $totalPrice;
            $validated['total_qty'] = $totalQty;
            $validated['total_tax_amount'] = $tax;
            $validated['total_amount'] = $grandTotal;
            $validated['is_paid'] = false;

            $validated['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();

            $bookingTransaction = BookingTransaction::create($validated);

            foreach ($products as $product) {
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $bookingTransaction->details()->create([
                    'cosmetic_id' => $product['id'],
                    'qty' => $product['qty'],
                    'price' => $cosmetic->price,
                ]);
            }

            return new BookingTransactionApiResource($bookingTransaction->load('details'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occured', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingTransaction $bookingTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookingTransaction $bookingTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingTransaction $bookingTransaction)
    {
        //
    }
}
