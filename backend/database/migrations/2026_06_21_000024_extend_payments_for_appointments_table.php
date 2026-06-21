<?php

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
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropForeign(['order_id']);
            $table->foreignId('order_id')->nullable()->change();
            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->index(['appointment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex(['appointment_id', 'status']);
            $table->dropConstrainedForeignId('appointment_id');
            $table->dropForeign(['order_id']);
            $table->foreignId('order_id')->nullable(false)->change();
            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
        });
    }
};
