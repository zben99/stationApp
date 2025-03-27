<x-app-layout>
    <x-slot name="header">
        Détails du Client
    </x-slot>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Nom :</strong>
                    <p>{{ $client->name }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Téléphone :</strong>
                    <p>{{ $client->phone }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Email :</strong>
                    <p>{{ $client->email ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Adresse :</strong>
                    <p>{{ $client->address ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Crédit :</strong>
                    <p>{{ number_format($client->credit_balance, 2) }} FCFA</p>
                </div>
                <div class="col-md-6">
                    <strong>Statut :</strong>
                    <p>
                        @if($client->is_active)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-12">
                    <strong>Notes :</strong>
                    <p>{{ $client->notes ?? '-' }}</p>
                </div>
            </div>

            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Retour</a>
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">Modifier</a>
        </div>
    </div>
</x-app-layout>
