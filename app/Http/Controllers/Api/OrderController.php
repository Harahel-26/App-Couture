<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller {
 private array $statuses=['reçue','tissu_valide','coupe','couture','essayage','pret','livree','annulee'];
 public function index(Request $request){return $request->user()->stylistOrders()->with(['client','measurementProfile','product','costs'])->latest()->paginate(20);}
 public function store(Request $request){
  $data=$request->validate(['client_id'=>['required','integer',Rule::exists('users','id')->where('role','client')],'measurement_profile_id'=>['nullable','integer',Rule::exists('measurement_profiles','id')->where('client_id',$request->input('client_id'))],'product_id'=>['nullable','integer',Rule::exists('products','id')->where('stylist_id',$request->user()->id)],'status'=>['sometimes',Rule::in($this->statuses)],'sale_price'=>'required|numeric|min:0','due_date'=>'nullable|date','notes'=>'nullable|string']);
  $data['stylist_id']=$request->user()->id; $order=Order::create($data); $order->costs()->create(); return response()->json($order->load(['client','measurementProfile','costs']),201);
 }
 public function update(Request $request, Order $order){abort_unless($order->stylist_id===$request->user()->id,403);$data=$request->validate(['status'=>['sometimes',Rule::in($this->statuses)],'sale_price'=>'sometimes|numeric|min:0','due_date'=>'nullable|date','notes'=>'nullable|string','measurement_profile_id'=>['nullable','integer',Rule::exists('measurement_profiles','id')->where('client_id',$order->client_id)]]);$order->update($data);return $order->load(['client','measurementProfile','costs']);}
 public function destroy(Request $request, Order $order){abort_unless($order->stylist_id===$request->user()->id,403);$order->delete();return response()->noContent();}
 public function clientIndex(Request $request){return $request->user()->clientOrders()->with(['stylist.stylistProfile','product','measurementProfile'])->latest()->paginate(20);}
 public function clientStore(Request $request){$data=$request->validate(['stylist_id'=>['required','integer',Rule::exists('users','id')->where('role','stylist')],'measurement_profile_id'=>['nullable','integer',Rule::exists('measurement_profiles','id')->where('client_id',$request->user()->id)],'product_id'=>['nullable','integer',Rule::exists('products','id')->where('stylist_id',$request->input('stylist_id'))],'sale_price'=>'required|numeric|min:0','due_date'=>'nullable|date','notes'=>'nullable|string']);$data['client_id']=$request->user()->id;return response()->json(Order::create($data),201);}
}
