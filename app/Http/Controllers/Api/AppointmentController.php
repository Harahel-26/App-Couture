<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AppointmentController extends Controller {
 public function index(Request $request){return $request->user()->appointments()->with('client')->orderBy('starts_at')->get();}
 public function store(Request $request){$clientId=$request->input('client_id');$data=$request->validate(['client_id'=>['required','integer',Rule::exists('users','id')->where('role','client')],'order_id'=>['nullable','integer',Rule::exists('orders','id')->where('stylist_id',$request->user()->id)->where('client_id',$clientId)],'type'=>'required|string|max:100','starts_at'=>'required|date','ends_at'=>'nullable|date','notes'=>'nullable|string']);$data['stylist_id']=$request->user()->id;return response()->json(Appointment::create($data),201);}
 public function show(Request $request, Appointment $appointment){abort_unless($appointment->stylist_id===$request->user()->id,404);return $appointment->load('client','order');}
 public function update(Request $request, Appointment $appointment){abort_unless($appointment->stylist_id===$request->user()->id,404);$request->mergeIfMissing(['order_id'=>$appointment->order_id]);$clientId=$request->input('client_id',$appointment->client_id);$appointment->update($request->validate(['client_id'=>['sometimes','integer',Rule::exists('users','id')->where('role','client')],'order_id'=>['nullable','integer',Rule::exists('orders','id')->where('stylist_id',$request->user()->id)->where('client_id',$clientId)],'type'=>'sometimes|string|max:100','starts_at'=>'sometimes|date','ends_at'=>'nullable|date','notes'=>'nullable|string']));return $appointment;}
 public function destroy(Request $request, Appointment $appointment){abort_unless($appointment->stylist_id===$request->user()->id,404);$appointment->delete();return response()->noContent();}
}
