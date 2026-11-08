<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderCost extends Model {
 protected $fillable=['order_id','materials','labor','fixed_costs'];
 protected $casts=['materials'=>'decimal:2','labor'=>'decimal:2','fixed_costs'=>'decimal:2'];
 public function order(){return $this->belongsTo(Order::class);}
}
