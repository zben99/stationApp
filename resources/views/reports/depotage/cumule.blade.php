<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            Dépotages carburant – État cumulé ({{ $start }} → {{ $end }})
        </h2>
    </x-slot>

    <div class="py-4 container">

        <!-- Formulaire de filtre -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>Date de début</label>
                <input type="date" name="start_date" class="form-control" value="{{ $start }}">
            </div>
            <div class="col-md-3">
                <label>Date de fin</label>
                <input type="date" name="end_date" class="form-control" value="{{ $end }}">
            </div>
            <div class="col-md-3">
                <label>Produit</label>
                <select name="product_id" class="form-control">
                    <option value="">-- Tous les produits --</option>
                    @foreach(\App\Models\StationProduct::where('category_id', 1)->get() as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                             {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>

        <!-- Tableau cumulé -->
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Produit | Cuve</th>
                    <th>Total dépoté (L)</th>
                    <th>Nombre de livraisons</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grouped as $key => $linesGroup)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ number_format($linesGroup->sum('reception_par_cuve'), 2) }}</td>
                        <td>{{ $linesGroup->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun résultat pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('reports.depotage.cumule.excel', [
                'start_date' => $start,
                'end_date' => $end,
                'product_id' => request('product_id')
            ]) }}" class="btn btn-success btn-sm">
                📥 Export Excel
            </a>

            <a href="{{ route('reports.depotage.cumule.pdf', [
                'start_date' => $start,
                'end_date' => $end,
                'product_id' => request('product_id')
            ]) }}" class="btn btn-danger btn-sm" target="_blank">
                🧾 Export PDF
            </a>
        </div>

        <br>
        <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>


    </div>
</x-app-layout>
