<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StylistProfile extends Model {
 protected $fillable=['user_id','shop_name','city','bio','published'];
 protected $casts=['published'=>'boolean'];
 public function user(){return $this->belongsTo(User::class);}
 public function products(){return $this->hasMany(Product::class,'stylist_id');}
}
