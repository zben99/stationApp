<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-dark">
            Approvisionnements en Lubrifiant / PEA / GAZ / Lampes / Divers
        </h2>

    </x-slot>

    <div class="py-4 container">

        <!-- Filtre période -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Date de début</label>
                <input type="date" name="start_date" class="form-control" value="{{ $start }}">
            </div>
            <div class="col-md-4">
                <label>Date de fin</label>
                <input type="date" name="end_date" class="form-control" value="{{ $end }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>

        <!-- Table appro lubrifiants -->
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Rotation</th>
                    <th>Produit</th>
                    <th>Conditionnement</th>
                    <th>Fournisseur</th>
                    <th>BC</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($batches as $batch)
                    @foreach ($batch->receptions as $d)
                        <tr>
                            <td>{{ $batch->date }}</td>
                            <td>{{ $batch->rotation }}</td>
                            <td>{{ $d->packaging->product->name ?? '-' }}</td>
                            <td>{{ $d->packaging->packaging->label ?? '-' }}</td>
                            <td>{{ $batch->supplier->name ?? '-' }}</td>

                             <td>{{ $batch->num_bc }}</td>
                            <td>{{ $d->quantite }}</td>
                            <td>{{ number_format($d->prix_achat, 2) }} F</td>
                            <td>{{ number_format($d->quantite * $d->prix_achat, 2) }} F</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucun approvisionnement trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('reports.supplies.lubricants.excel', ['start_date' => $start, 'end_date' => $end]) }}"
            class="btn btn-success btn-sm">
            📥 Export Excel
            </a>

            <a href="{{ route('reports.supplies.lubricants.pdf', ['start_date' => $start, 'end_date' => $end]) }}"
            class="btn btn-danger btn-sm" target="_blank">
            🧾 Export PDF
            </a>
        </div>

        <br>
          <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>


    </div>
</x-app-layout>
