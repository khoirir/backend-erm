<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_erm', function (Blueprint $table) {
            $table->string("id", 50)
                ->nullable(false)
                ->unique()
                ->primary();
            $table->string("kd_dokter", 20)
                ->nullable(false)
                ->charset('latin1')
                ->collation('latin1_swedish_ci');
            $table->timestamp("expired_at")
                ->nullable(false)
                ->default(DB::raw('DATE_ADD(NOW(), INTERVAL 5 DAY)'));
            $table->timestamps();

            $table->foreign("kd_dokter")->on("dokter")->references("kd_dokter");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_erm');
    }
};
