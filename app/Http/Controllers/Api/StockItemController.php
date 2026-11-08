<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StockItem;
use Illuminate\Http\Request;
class StockItemController extends Controller {
 public function index(Request $request){return $request->user()->stockItems()->orderBy('name')->get()->each(fn($i)=>$i->append('is_critical'));}
 public function store(Request $request){$data=$request->validate(['name'=>'required|string|max:180','category'=>'required|string|max:80','quantity'=>'required|numeric|min:0','unit'=>'required|string|max:20','min_threshold'=>'required|numeric|min:0']);$data['stylist_id']=$request->user()->id;return response()->json(StockItem::create($data)->append('is_critical'),201);}
 public function show(Request $request, StockItem $stock_item){abort_unless($stock_item->stylist_id===$request->user()->id,404);return $stock_item->append('is_critical');}
 public function update(Request $request, StockItem $stock_item){abort_unless($stock_item->stylist_id===$request->user()->id,404);$stock_item->update($request->validate(['name'=>'sometimes|string|max:180','category'=>'sometimes|string|max:80','quantity'=>'sometimes|numeric|min:0','unit'=>'sometimes|string|max:20','min_threshold'=>'sometimes|numeric|min:0']));return $stock_item->append('is_critical');}
 public function destroy(Request $request, StockItem $stock_item){abort_unless($stock_item->stylist_id===$request->user()->id,404);$stock_item->delete();return response()->noContent();}
}
