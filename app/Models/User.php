<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['role','name','email','phone','password'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed']; }
    public function stylistProfile(){ return $this->hasOne(StylistProfile::class); }
    public function measurementProfiles(){ return $this->hasMany(MeasurementProfile::class, 'client_id'); }
    public function stylistOrders(){ return $this->hasMany(Order::class, 'stylist_id'); }
    public function products(){ return $this->hasMany(Product::class, 'stylist_id'); }
    public function clientOrders(){ return $this->hasMany(Order::class, 'client_id'); }
    public function stockItems(){ return $this->hasMany(StockItem::class, 'stylist_id'); }
    public function appointments(){ return $this->hasMany(Appointment::class, 'stylist_id'); }
}
