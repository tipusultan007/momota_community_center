<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::get();

        return response()->json([
            'status' => 'success',
            'data' => $assets,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::create([
            'name' => $request->name,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->total_stock,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'মালামাল সফলভাবে যোগ করা হয়েছে।',
            'data' => $asset,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'total_stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Adjust available_stock based on change in total_stock
        $stockDiff = $request->total_stock - $asset->total_stock;
        
        $asset->update([
            'name' => $request->name,
            'total_stock' => $request->total_stock,
            'available_stock' => $asset->available_stock + $stockDiff,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'মালামাল সফলভাবে আপডেট করা হয়েছে।',
            'data' => $asset,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->assignments()->where('quantity_in', '<', 'quantity_out')->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'এই মালামালটি বর্তমানে কোনো বুকিংয়ে বরাদ্দ দেওয়া আছে, তাই এটি মুছে ফেলা সম্ভব নয়।',
            ], 400);
        }

        $asset->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'মালামাল সফলভাবে মুছে ফেলা হয়েছে।',
        ]);
    }
}
