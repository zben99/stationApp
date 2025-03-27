<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        if (!$stationId) {
            return redirect()->route('station.selection')->with('error', 'Veuillez sélectionner une station.');
        }

        $categories = ExpenseCategory::where('station_id', $stationId)->paginate(10);

        return view('expense_categories.index', compact('categories'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('expense_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['is_active'] = $request->has('is_active');

        ExpenseCategory::create($data);

        return redirect()->route('expense-categories.index')->with('success', 'Rubrique créée avec succès.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $expenseCategory->update($data);

        return redirect()->route('expense-categories.index')->with('success', 'Rubrique mise à jour avec succès.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Rubrique supprimée avec succès.');
    }
}
