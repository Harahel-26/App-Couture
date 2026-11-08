<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
class StylistController extends Controller {
 public function dashboard(Request $request){
  $user=$request->user(); $orders=$user->stylistOrders()->with(['client','costs'])->latest()->get();
  $revenue=$orders->sum(fn($o)=>(float)$o->sale_price);
  $cost=$orders->sum(fn($o)=>$o->costs ? (float)$o->costs->materials+(float)$o->costs->labor+(float)$o->costs->fixed_costs : 0);
  return ['orders_active'=>$orders->whereNotIn('status',['pret','livree','annulee'])->count(),'revenue'=>$revenue,'margin'=>$revenue>0?round(($revenue-$cost)/$revenue*100,1):0,'stock_alerts'=>$user->load('stylistProfile')->stylistProfile ? $user->stockItems()->whereColumn('quantity','<','min_threshold')->count():0,'orders'=>$orders];
 }
 public function analytics(Request $request){$orders=$request->user()->stylistOrders()->with('costs')->get();$revenue=$orders->sum('sale_price');$cost=$orders->sum(fn($o)=>$o->costs?(float)$o->costs->materials+(float)$o->costs->labor+(float)$o->costs->fixed_costs:0);return ['revenue'=>$revenue,'cost'=>$cost,'gross_margin'=>$revenue-$cost,'orders_count'=>$orders->count()];}
}
