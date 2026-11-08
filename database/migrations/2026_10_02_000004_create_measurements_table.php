<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(){Schema::create('measurements',function(Blueprint $t){$t->id();$t->foreignId('profile_id')->constrained('measurement_profiles')->cascadeOnDelete();foreach(['chest','waist','hips','shoulder','sleeve','length'] as $c)$t->decimal($c,6,2)->nullable();$t->timestamp('recorded_at')->useCurrent();});} public function down(){Schema::dropIfExists('measurements');} };
