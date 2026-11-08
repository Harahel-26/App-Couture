<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
 protected $fillable=['stylist_id','name','category','price','description','published'];
 protected $casts=['published'=>'boolean','price'=>'decimal:2'];
 public function stylist(){return $this->belongsTo(User::class,'stylist_id');}
}
