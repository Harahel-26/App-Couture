<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Appointment extends Model {
 protected $fillable=['stylist_id','client_id','order_id','type','starts_at','ends_at','notes'];
 protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime'];
 public function stylist(){return $this->belongsTo(User::class,'stylist_id');}
 public function client(){return $this->belongsTo(User::class,'client_id');}
 public function order(){return $this->belongsTo(Order::class);}
}
