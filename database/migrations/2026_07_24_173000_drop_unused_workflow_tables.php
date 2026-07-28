<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('outward_items');
        Schema::dropIfExists('outwards');
        Schema::dropIfExists('material_receive_items');
        Schema::dropIfExists('material_receives');
    }

    public function down(): void {
        // No rollback needed for unused cleanup tables
    }
};
