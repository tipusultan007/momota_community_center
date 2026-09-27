<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::withCount([
            'bookings' => fn($q) => $q->withoutGlobalScopes(),
            'incomes' => fn($q) => $q->withoutGlobalScopes(),
            'expenses' => fn($q) => $q->withoutGlobalScopes(),
            'commissions' => fn($q) => $q->withoutGlobalScopes(),
            'assets' => fn($q) => $q->withoutGlobalScopes(),
        ])->get();
        return view('halls.index', compact('halls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('halls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer',
            'price_per_slot' => 'required|numeric',
        ]);

        Hall::create($request->all());

        return redirect()->route('halls.index')->with('success', 'নতুন হল সফলভাবে যোগ করা হয়েছে।');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        return view('halls.show', compact('hall'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hall $hall)
    {
        return view('halls.edit', compact('hall'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer',
            'price_per_slot' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $hall->update($data);

        return redirect()->route('halls.index')->with('success', 'হলের তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        // Don't allow deleting the last hall
        if (Hall::count() <= 1) {
            return redirect()->route('halls.index')->with('error', 'অন্তত একটি হল সচল থাকতে হবে। আপনি আপনার একমাত্র হলটি ডিলিট করতে পারবেন না।');
        }

        // Deletion will cascade to bookings, incomes, expenses, assets, and commissions
        $hall->delete();

        return redirect()->route('halls.index')->with('success', 'হল এবং এর সাথে সংশ্লিষ্ট সকল ডাটা সফলভাবে ডিলিট করা হয়েছে।');
    }
}
