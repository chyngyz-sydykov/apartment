<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->integer('area');
            $table->integer('room_number');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->float('price');
            $table->boolean('is_active')->default(false);
            $table->foreignId('city_id')->constrained('cities')->nullOnDelete();
            $table->foreignId('user_id')->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
