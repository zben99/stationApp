<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">Rapport des ventes carburant</h2>
    </x-slot>

    <div class="py-4 container">
        <!-- Filtres -->
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-4">
                <select name="rotation" class="form-control">
                    <option value="6-14" {{ $rotation == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ $rotation == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ $rotation == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Voir les ventes</button>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('fuel-reports.export-excel', ['date' => $date, 'rotation' => $rotation]) }}"
            class="btn btn-outline-success me-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('fuel-reports.export-pdf', ['date' => $date, 'rotation' => $rotation]) }}"
            class="btn btn-outline-danger">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>


        <!-- Tableau -->
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Pompiste</th>
                            <th>Pompe</th>
                            <th>Produit</th>
                            <th>Index Début</th>
                            <th>Index Fin</th>
                            <th>Retour Cuve</th>
                            <th>Vente</th>
                            <th>Prix Unitaire</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fuelIndexes as $index)
                            <tr>
                                <td>{{ $index->user->name ?? '-' }}</td>
                                <td>{{ $index->pump->name ?? '-' }}</td>
                                <td>{{ $index->pump->tank->product->name ?? '-' }}</td>
                                <td>{{ number_format($index->index_debut, 2) }}</td>
                                <td>{{ number_format($index->index_fin, 2) }}</td>
                                <td>{{ number_format($index->retour_en_cuve, 2) }}</td>
                                <td>{{ number_format(($index->index_fin - $index->index_debut - $index->retour_en_cuve), 2) }}</td>
                                <td>{{ number_format($index->prix_unitaire, 2) }}</td>
                                <td>{{ number_format($index->montant_recette, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">Aucune donnée trouvée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
          <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>

    </div>




</x-app-layout>
