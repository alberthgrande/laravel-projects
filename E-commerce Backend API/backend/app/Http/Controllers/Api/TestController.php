<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public function index() : JsonResponse {
        return response()->json([
            'status'=> 'success',
            'message'=> 'Laravel API is working!',
            'timestamp' => now(),
        ]);
    }
}
