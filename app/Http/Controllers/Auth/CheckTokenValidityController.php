<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CheckTokenValidityController extends Controller
{
    public function checkIsAuthenticated(Request $request)
{
   return response()->json(['isAuthenticated' => true]);
}
}
