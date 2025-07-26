<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            Rapport des crédits clients
        </h2>
    </x-slot>

    <div class="py-4 container">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <label>Client</label>
                <select name="client_id" class="form-control">
                    <option value="">-- Tous les clients --</option>
                    @foreach(\App\Models\Client::where('station_id', session('selected_station_id'))->get() as $client)
                        <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>
                            {{ $client->name }} ({{ $client->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Nom client</th>
                    <th>Téléphone</th>
                    <th>Crédit reçu (F)</th>
                    <th>Remboursé (F)</th>
                    <th>Solde restant (F)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $c)
                    <tr>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->phone }}</td>
                        <td>{{ number_format($c->credit, 2) }}</td>
                        <td>{{ number_format($c->repayment, 2) }}</td>
                        <td class="{{ $c->balance < 0 ? 'text-danger' : '' }}">
                            {{ number_format($c->balance, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Aucun client trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('reports.clients.credits.excel', ['client_id' => request('client_id')]) }}"
            class="btn btn-success btn-sm">
                📥 Export Excel
            </a>

            <a href="{{ route('reports.clients.credits.pdf', ['client_id' => request('client_id')]) }}"
            class="btn btn-danger btn-sm" target="_blank">
                🧾 Export PDF
            </a>
        </div>

          <br>
        <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>


    </div>
</x-app-layout>
