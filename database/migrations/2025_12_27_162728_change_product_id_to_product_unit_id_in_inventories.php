<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {

            // 1. ลบ foreign key เดิม
            $table->dropForeign(['product_id']);

            // 2. ลบ column เดิม
            $table->dropColumn('product_id');

            // 3. เพิ่ม column ใหม่
            $table->foreignId('product_unit_id')
                  ->after('id')
                  ->constrained('product_units')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {

            $table->dropForeign(['product_unit_id']);
            $table->dropColumn('product_unit_id');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
        });
    }
};
