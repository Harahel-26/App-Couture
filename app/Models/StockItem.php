<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockItem extends Model {
 protected $fillable=['stylist_id','name','category','quantity','unit','min_threshold'];
 protected $casts=['quantity'=>'decimal:2','min_threshold'=>'decimal:2'];
 public function stylist(){return $this->belongsTo(User::class,'stylist_id');}
 public function getIsCriticalAttribute(){return $this->quantity < $this->min_threshold;}
}
