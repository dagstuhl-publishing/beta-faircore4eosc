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
        Schema::create("swh_archives", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable(false)
                ->constrained()->restrictOnUpdate()->cascadeOnDelete();

            $table->string("originUrl")->nullable(false);

            $table->unsignedBigInteger("saveRequestId")->nullable(true);
            $table->string("saveRequestStatus")->nullable(true);
            $table->string("saveTaskStatus")->nullable(true);
            $table->string("visitStatus")->nullable(true);
            $table->string("swhId")->nullable(true);
            $table->string("swhIdContext")->nullable(true);

            $table->timestamps();
            $table->timestamp("requested_at")->nullable(true);
            $table->timestamp("finished_at")->nullable(true);

            $table->unique([ "user_id", "id" ]);
            $table->index([ "finished_at", "id" ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("swh_archives");
    }
};
