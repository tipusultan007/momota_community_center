<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->paginate(10);
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $asset = new Asset($request->all());
        $asset->available_stock = $request->total_stock;
        $asset->save();

        return redirect()->route('assets.index')->with('success', 'মালামাল সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Adjust available stock based on new total stock
        $diff = $request->total_stock - $asset->total_stock;
        $asset->available_stock += $diff;
        
        $asset->update($request->all());

        return redirect()->route('assets.index')->with('success', 'মালামাল সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->assignments()->exists()) {
            return redirect()->back()->with('error', 'এই মালামালটি ব্যবহার করা হয়েছে, তাই এটি ডিলিট করা সম্ভব নয়।');
        }

        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'মালামাল সফলভাবে ডিলিট করা হয়েছে।');
    }
}
