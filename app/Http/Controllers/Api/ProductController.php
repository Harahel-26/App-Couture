<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller {
 public function index(Request $request){return $request->user()->stylistProfile?->products()->latest()->get() ?? [];}
 public function store(Request $request){$data=$request->validate(['name'=>'required|string|max:180','category'=>'required|in:sur_mesure,pret_a_porter','price'=>'required|numeric|min:0','description'=>'nullable|string','published'=>'boolean']);$data['stylist_id']=$request->user()->id;return response()->json(Product::create($data),201);}
 public function show(Request $request, Product $product){abort_unless($product->stylist_id===$request->user()->id,404);return $product;}
 public function update(Request $request, Product $product){abort_unless($product->stylist_id===$request->user()->id,404);$product->update($request->validate(['name'=>'sometimes|string|max:180','category'=>'sometimes|in:sur_mesure,pret_a_porter','price'=>'sometimes|numeric|min:0','description'=>'nullable|string','published'=>'boolean']));return $product;}
 public function destroy(Request $request, Product $product){abort_unless($product->stylist_id===$request->user()->id,404);$product->delete();return response()->noContent();}
}
