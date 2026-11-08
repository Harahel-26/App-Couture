<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ClientController extends Controller {
 public function index(Request $request){return $request->user()->stylistOrders()->select('client_id')->with('client')->get()->pluck('client')->unique('id')->values();}
 public function show(Request $request, User $client){abort_unless($request->user()->stylistOrders()->where('client_id',$client->id)->exists(),404);return $client->load('measurementProfiles.measurements');}
 public function store(Request $request){$data=$request->validate(['name'=>'required|string|max:160','phone'=>'required|string|max:30|unique:users,phone','email'=>'nullable|email|max:190|unique:users,email']);$data['role']='client';$data['password']=str()->random(32);return response()->json(User::create($data),201);}
 public function update(Request $request, User $client){abort_unless($request->user()->stylistOrders()->where('client_id',$client->id)->exists(),404);$data=$request->validate(['name'=>'sometimes|string|max:160','phone'=>['sometimes','string','max:30',Rule::unique('users','phone')->ignore($client->id)],'email'=>['sometimes','nullable','email','max:190',Rule::unique('users','email')->ignore($client->id)]]);$client->update($data);return $client;}
}
