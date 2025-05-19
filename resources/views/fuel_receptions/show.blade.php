<x-app-layout>
    <x-slot name="header">Détail du Dépotage</x-slot>

    <div class="row">
        <div class="col-1"></div>

        <div class="col-7">
            <a href="{{ route('fuel-receptions.export.pdf', $reception->id) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf"></i> Exporter en PDF
            </a>
            <a href="{{ route('fuel-receptions.export', $reception->id) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exporter en Excel
            </a>
        </div>

        <div class="col-4"></div>
    </div>

    <div class="card shadow mb-4 p-4">
        <h5>Informations générales</h5>
        <table class="table table-bordered">
            <tr><th>Date réception</th><td>{{ \Carbon\Carbon::parse($reception->date_reception)->format('d/m/Y') }}</td></tr>
            <tr><th>Rotation</th><td>{{ $reception->rotation ?? '-' }}</td></tr>
            <tr><th>Station</th><td>{{ $reception->station->name ?? '-' }}</td></tr>
            <tr><th>Transporteur</th><td>{{ $reception->transporter->name ?? '-' }}</td></tr>
            <tr><th>Chauffeur</th><td>{{ $reception->driver->name ?? '-' }}</td></tr>
            <tr><th>Numéro BL</th><td>{{ $reception->num_bl ?? '-' }}</td></tr>
            <tr><th>Commentaire</th><td>{{ $reception->remarques ?? '-' }}</td></tr>
        </table>

        <h5 class="mt-4">Détails par cuve</h5>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Cuve</th>
                    <th>Produit</th>
                    <th>Jauge avant</th>
                    <th>Quantité reçue</th>
                    <th>Jauge après</th>
                    <th>Écart réception</th>
                    <th>Écart stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reception->lines as $line)
                    <tr>
                        <td>{{ $line->tank->code ?? '-' }}</td>
                        <td>{{ $line->tank->product->name ?? '-' }}</td>
                        <td>{{ $line->jauge_avant ?? '—' }}</td>
                        <td>{{ $line->reception_par_cuve ?? '—' }}</td>
                        <td>{{ $line->jauge_apres ?? '—' }}</td>
                        <td class="{{ abs($line->ecart_reception) > 20 ? 'text-danger' : 'text-success' }}">
                            {{ $line->ecart_reception ?? '—' }} L
                        </td>
                        <td class="{{ abs($line->ecart_stock) > 20 ? 'text-warning' : '' }}">
                            {{ $line->ecart_stock ?? '—' }} L
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('fuel-receptions.index') }}" class="btn btn-secondary mt-3">Retour</a>
    </div>
</x-app-layout>
