<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // TODO: Check if need to change name. This is called public_id because it'll show in the receipt for the user
            $table->ulid('public_id');
            $table->foreignIdFor(User::class)->constrained();
            $table->string('payment_method');
            $table->string('payment_gateway');
            $table->string('payment_id');
            $table->string('status')->default('pending');
            $table->string('reference')->nullable();
            $table->string('pdf_url')->nullable();
            $table->decimal('subtotal', 8, 2);
            $table->decimal('tax', 8, 2);
            $table->decimal('shipping', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();

            $table->index('public_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
