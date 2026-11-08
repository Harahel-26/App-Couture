<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
class CatalogController extends Controller {
 public function index(){return User::where('role','stylist')->whereHas('stylistProfile',fn($q)=>$q->where('published',true))->with('stylistProfile')->get();}
 public function show(User $stylist){abort_unless($stylist->role==='stylist',404);return $stylist->load(['stylistProfile','stylistProfile.products'=>fn($q)=>$q->where('published',true)]);}
}
