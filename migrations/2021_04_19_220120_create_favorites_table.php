<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            config('favorite.table_names.pivot'),
            static function (Blueprint $table): void {
                config('favorite.uuids') ? $table->uuid('uuid') : $table->bigIncrements('id');
                $table->unsignedBigInteger(config('favorite.column_names.user_foreign_key'))
                    ->index()
                    ->comment('user_id');
                $table->morphs('favoriteable');
                $table->timestamps();
                $table->unique(
                    [config('favorite.column_names.user_foreign_key'), 'favoriteable_type', 'favoriteable_id']
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('favorite.table_names.favorites'));
    }
}
