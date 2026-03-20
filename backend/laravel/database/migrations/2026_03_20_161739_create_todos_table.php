<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {

            $table->uuid('id')->primary();


            $table->string('title');


            $table->uuid('assignee_id')->nullable();

            $table->date('due_date');

            $table->unsignedInteger('time_tracked')->default(0);

            $table->enum('status', ['pending', 'open', 'in_progress', 'completed'])->default('pending');

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->timestamps();

            $table->foreign('assignee_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('due_date');
            $table->index('status');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};