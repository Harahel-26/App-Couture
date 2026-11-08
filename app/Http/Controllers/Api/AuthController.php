<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller {
 public function register(Request $request){
  $data=$request->validate(['role'=>['required','in:stylist,client'],'name'=>'required|string|max:160','email'=>'required_without:phone|nullable|email|max:190|unique:users,email','phone'=>'required_without:email|nullable|string|max:30|unique:users,phone','password'=>['required','confirmed',Password::min(8)]]);
  $user=DB::transaction(function () use ($data) {
   $user=User::create($data);
   if($user->role==='stylist') $user->stylistProfile()->create(['shop_name'=>$user->name,'city'=>'Cotonou']);
   return $user;
  });
  return response()->json(['user'=>$user,'token'=>$user->createToken('web')->plainTextToken],201);
 }
 public function login(Request $request){
  $data=$request->validate(['email'=>'nullable|email','phone'=>'nullable|string','password'=>'required']);
  $user=User::when($data['email']??null,fn($q,$v)=>$q->where('email',$v))->when(!($data['email']??null),fn($q)=>$q->where('phone',$data['phone']??''))->first();
  abort_unless($user && Hash::check($data['password'],$user->password),422,'Identifiants invalides.');
  return ['user'=>$user,'token'=>$user->createToken('web')->plainTextToken];
 }
 public function me(Request $request){return $request->user()->load('stylistProfile');}
 public function logout(Request $request){$request->user()->currentAccessToken()?->delete();return response()->noContent();}
}
