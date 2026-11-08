<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(){Schema::create('order_costs',function(Blueprint $t){$t->id();$t->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();$t->decimal('materials',12,2)->default(0);$t->decimal('labor',12,2)->default(0);$t->decimal('fixed_costs',12,2)->default(0);$t->timestamps();});} public function down(){Schema::dropIfExists('order_costs');} };
