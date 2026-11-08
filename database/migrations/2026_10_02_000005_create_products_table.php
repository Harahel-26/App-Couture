<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(){Schema::create('products',function(Blueprint $t){$t->id();$t->foreignId('stylist_id')->constrained('users')->cascadeOnDelete();$t->string('name',180);$t->enum('category',['sur_mesure','pret_a_porter']);$t->decimal('price',12,2)->default(0);$t->text('description')->nullable();$t->boolean('published')->default(true);$t->timestamps();});} public function down(){Schema::dropIfExists('products');} };
