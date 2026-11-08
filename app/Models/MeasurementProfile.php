<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MeasurementProfile extends Model {
 protected $fillable=['client_id','label','notes'];
 public function client(){return $this->belongsTo(User::class,'client_id');}
 public function measurements(){return $this->hasMany(Measurement::class,'profile_id')->latest('recorded_at');}
}
