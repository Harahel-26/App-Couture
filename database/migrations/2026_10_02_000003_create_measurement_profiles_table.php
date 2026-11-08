<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(){Schema::create('measurement_profiles',function(Blueprint $t){$t->id();$t->foreignId('client_id')->constrained('users')->cascadeOnDelete();$t->string('label',120);$t->text('notes')->nullable();$t->timestamps();});} public function down(){Schema::dropIfExists('measurement_profiles');} };
