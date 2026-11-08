<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Measurement extends Model {
 public $timestamps=false;
 protected $fillable=['profile_id','chest','waist','hips','shoulder','sleeve','length','recorded_at'];
 protected $casts=['recorded_at'=>'datetime'];
 public function profile(){return $this->belongsTo(MeasurementProfile::class,'profile_id');}
}
