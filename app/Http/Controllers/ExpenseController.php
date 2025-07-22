<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\DailyRevenueValidation;
use App\Models\ModificationLog;
use App\Traits\ValidatesRotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    use ValidatesRotation;
    public function index()
    {
        $stationId = session('selected_station_id');
        $expenses = Expense::with('category')
            ->where('station_id', $stationId)
            ->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');
        $categories = ExpenseCategory::where('station_id', $stationId)->where('is_active', true)->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date_depense' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'piece_jointe' => 'nullable|file|max:2048',
        ]);

        $data = $request->all();
        $data['station_id'] = $stationId;

        if ($request->hasFile('piece_jointe')) {
            $data['piece_jointe'] = $request->file('piece_jointe')->store('justificatifs');
        }

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Dépense enregistrée.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('station_id', $expense->station_id)->where('is_active', true)->get();

        $isValidated = DailyRevenueValidation::where('station_id', $expense->station_id)
            ->where('date', $expense->date_depense)
            ->where('rotation', $expense->rotation)
            ->exists();

        if (! $this->modificationAllowed(
            $expense->station_id,
            $expense->date_depense,
            $expense->rotation
        )) {
            return redirect()->route('expenses.index')->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date_depense' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'piece_jointe' => 'nullable|file|max:2048',
        ]);

        $data = $request->all();

        if (! $this->modificationAllowed(
            $expense->station_id,
            $expense->date_depense,
            $expense->rotation
        )) {
            return redirect()->route('expenses.index')->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        if ($request->hasFile('piece_jointe')) {
            if ($expense->piece_jointe) {
                Storage::delete($expense->piece_jointe);
            }
            $data['piece_jointe'] = $request->file('piece_jointe')->store('justificatifs');
        }

        $before = $expense->getOriginal();

        $expense->update($data);

        if ($isValidated) {
            $this->logOverride($expense, $before, $expense->getAttributes());
        }

        return redirect()->route('expenses.index')->with('success', 'Dépense modifiée.');
    }

    public function destroy(Expense $expense)
    {
        $isValidated = DailyRevenueValidation::where('station_id', $expense->station_id)
            ->where('date', $expense->date_depense)
            ->where('rotation', $expense->rotation)
            ->exists();

        if (! $this->modificationAllowed(
            $expense->station_id,
            $expense->date_depense,
            $expense->rotation
        )) {
            return redirect()->route('expenses.index')->with('error', 'Cette rotation a déjà été validée. Suppression impossible.');
        }

        if ($expense->piece_jointe) {
            Storage::delete($expense->piece_jointe);
        }

        $before = $expense->getOriginal();
        $expense->delete();

        if ($isValidated) {
            $this->logOverride($expense, $before, []);
        }

        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée.');
    }
}
