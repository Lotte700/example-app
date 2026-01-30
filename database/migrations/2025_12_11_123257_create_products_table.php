
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // รหัสสินค้า เช่น PD001
            $table->string('name');
            $table->foreignId('category_id')
            ->constrained('categories');
            $table->string('image')->nullable();     // รูปสินค้า (เก็บ filename)
            $table->text('description')->nullable(); // รายละเอียดสินค้า
            $table->timestamps();                    // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

