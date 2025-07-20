<x-app-layout>
    <x-slot name="header">Détails du lot de réception</x-slot>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Date de réception :</strong> {{ $batch->date_reception->format('d/m/Y') }}</p>
            <p><strong>Rotation :</strong> {{ $batch->rotation ?? '-' }}</p>
            <p><strong>Fournisseur :</strong> {{ $batch->supplier->name ?? 'N/A' }}</p>
            <p><strong>N° BC :</strong> {{ $batch->num_bc ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Produits reçus
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Conditionnement</th>
                            <th>Quantité</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->receptions as $rec)
                            <tr>
                                <td>{{ $rec->product->name }}</td>
                                <td>{{ $rec->packaging->packaging->label ?? '?' }}</td>
                                <td>{{ $rec->quantite }}</td>
                                <td>{{ $rec->observations ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('lubricant-receptions.batch.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <a href="{{ route('lubricant-receptions.batch.edit', $batch->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Modifier le lot
        </a>
    </div>
</x-app-layout>
