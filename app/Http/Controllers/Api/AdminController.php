<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\StylistProfile;
use Illuminate\Http\Request;
class AdminController extends Controller {
 public function dashboard(){return ['stylists'=>User::where('role','stylist')->count(),'clients'=>User::where('role','client')->count(),'orders'=>Order::count(),'published_stylists'=>StylistProfile::where('published',true)->count()];}
 public function stylists(){return User::where('role','stylist')->with('stylistProfile')->withCount('stylistOrders')->latest()->get();}
 public function orders(){return Order::with(['stylist.stylistProfile','client','product'])->latest()->paginate(30);}
 public function updateStylist(Request $request, User $stylist){abort_unless($stylist->role==='stylist',404);$data=$request->validate(['published'=>'required|boolean']);$stylist->stylistProfile()->updateOrCreate([],['shop_name'=>$stylist->name,'published'=>$data['published']]);return $stylist->load('stylistProfile');}
}
