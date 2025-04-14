<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use Illuminate\Http\Request;

class PackagingController extends Controller
{
    public function index()
    {
        $packagings = Packaging::latest()->get();
        return view('packagings.index', compact('packagings'));
    }

    public function create()
    {
        return view('packagings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'volume_litre' => 'required|numeric|min:0.01',
        ]);

        Packaging::create($request->only('label', 'volume_litre'));

        return redirect()->route('packagings.index')->with('success', 'Conditionnement ajouté.');
    }

    public function edit(Packaging $packaging)
    {
        return view('packagings.edit', compact('packaging'));
    }

    public function update(Request $request, Packaging $packaging)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'volume_litre' => 'required|numeric|min:0.01',
        ]);

        $packaging->update($request->only('label', 'volume_litre'));

        return redirect()->route('packagings.index')->with('success', 'Conditionnement modifié.');
    }

    public function destroy(Packaging $packaging)
    {
        $packaging->delete();
        return redirect()->route('packagings.index')->with('success', 'Conditionnement supprimé.');
    }
}
