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
        Schema::create("swh_deposits", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->nullable(false)->unique();
            $table->foreignId("user_id")->nullable(false)
                ->constrained()->restrictOnUpdate()->cascadeOnDelete();

            $table->string("archiveFilename")->nullable(true);
            $table->string("archiveContentType")->nullable(true);
            $table->unsignedBigInteger("archiveSize")->nullable(true);
            $table->string("archivePath")->nullable(true);
            $table->string("originSwhId")->nullable(true);
            $table->json("codemetaJson")->nullable(false);

            $table->unsignedSmallInt("latestResponseStatus")->nullable(true);
            $table->text("latestResponseBody")->nullable(true);
            $table->unsignedBigInteger("depositId")->nullable(true);
            $table->string("depositStatus")->nullable(true);
            $table->string("depositSwhId")->nullable(true);
            $table->string("depositSwhIdContext", 512)->nullable(true);

            $table->timestamps();
            $table->timestamp("deposited_at")->nullable(true);
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
        Schema::dropIfExists("swh_deposits");
    }
};
