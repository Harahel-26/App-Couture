<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(){Schema::create('stylist_profiles',function(Blueprint $t){$t->id();$t->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();$t->string('shop_name',180);$t->string('city',100)->nullable();$t->text('bio')->nullable();$t->boolean('published')->default(false);$t->timestamps();});} public function down(){Schema::dropIfExists('stylist_profiles');} };
