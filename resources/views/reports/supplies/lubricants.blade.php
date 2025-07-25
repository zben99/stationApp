<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            Approvisionnements en lubrifiants et PEA ({{ $start }} → {{ $end }})
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
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($batches as $batch)
                    @foreach ($batch->details as $d)
                        <tr>
                            <td>{{ $batch->date }}</td>
                            <td>{{ $batch->rotation }}</td>
                            <td>{{ $d->productPackaging->product->name ?? '-' }}</td>
                            <td>{{ $d->productPackaging->label ?? '-' }}</td>
                            <td>{{ $batch->supplier->name ?? '-' }}</td>
                            <td>{{ $d->quantity }}</td>
                            <td>{{ number_format($d->unit_price, 2) }} F</td>
                            <td>{{ number_format($d->quantity * $d->unit_price, 2) }} F</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucun approvisionnement trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
