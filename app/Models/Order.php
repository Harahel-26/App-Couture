<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
 protected $fillable=['stylist_id','client_id','measurement_profile_id','product_id','status','sale_price','due_date','notes'];
 protected $casts=['sale_price'=>'decimal:2','due_date'=>'date'];
 public function stylist(){return $this->belongsTo(User::class,'stylist_id');}
 public function client(){return $this->belongsTo(User::class,'client_id');}
 public function measurementProfile(){return $this->belongsTo(MeasurementProfile::class);}
 public function product(){return $this->belongsTo(Product::class);}
 public function costs(){return $this->hasOne(OrderCost::class);}
}
