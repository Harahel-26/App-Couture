<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\User; use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder { public function run(){
 if(app()->isProduction()) throw new \RuntimeException('The demo seeder must not be run in production.');
 $admin=User::create(['role'=>'admin','name'=>'Administration Atelier','email'=>'admin@atelier.local','phone'=>null,'password'=>Hash::make('ChangeMe123!')]);
 $stylist=User::create(['role'=>'stylist','name'=>'Afi','email'=>'afi@atelier.local','phone'=>'+22997000000','password'=>Hash::make('ChangeMe123!')]);
 $stylist->stylistProfile()->create(['shop_name'=>'Maison Afi','city'=>'Cotonou','bio'=>'Maison de création sur mesure.','published'=>true]);
 $client=User::create(['role'=>'client','name'=>'Nadia A.','email'=>'nadia@atelier.local','phone'=>'+22997001122','password'=>Hash::make('ChangeMe123!')]);
 $profile=$client->measurementProfiles()->create(['label'=>'Nadia — principal','notes'=>'Profil de démonstration']);$profile->measurements()->create(['chest'=>92,'waist'=>74,'hips'=>98,'shoulder'=>39,'sleeve'=>58,'length'=>145]);
 }}
