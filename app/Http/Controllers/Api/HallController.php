<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function index(Request $request)
    {
        $halls = Hall::get();

        return response()->json([
            'status' => 'success',
            'data' => $halls,
        ]);
    }

    public function show(Request $request, $id)
    {
        $hall = Hall::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $hall,
        ]);
    }
}
