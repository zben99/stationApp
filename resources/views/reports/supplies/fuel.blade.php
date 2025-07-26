<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            Approvisionnements en carburant ({{ $start }} → {{ $end }})
        </h2>
    </x-slot>

    <div class="py-4 container">

        <!-- Filtres -->
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

        <!-- Tableau -->
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Rotation</th>
                    <th>Produit</th>
                    <th>Cuve</th>
                    <th>Transporteur</th>

                    <th>BL</th>
                    <th>Qté reçue (L)</th>
                    <th>Montant total (achat)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($receptions as $reception)
                    @foreach ($reception->lines as $line)
                        <tr>
                            <td>{{ $reception->date_reception->format('Y-m-d') }}</td>
                            <td>{{ $reception->rotation }}</td>
                            <td>{{ $line->tank->product->name ?? '-' }}</td>
                            <td>{{ $line->tank->name ?? '-' }}</td>
                            <td>{{ $reception->transporter->name ?? '-' }}</td>

                            <td>{{ $reception->num_bl }}</td>

                            <td>{{ number_format($line->reception_par_cuve, 2) }}</td>


                            <td>{{ number_format($line->reception_par_cuve * $line->unit_price_purchase, 2) }} F</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Aucun approvisionnement trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('reports.supplies.fuel.excel', ['start_date' => $start, 'end_date' => $end]) }}"
            class="btn btn-success btn-sm">
            📥 Export Excel
            </a>

            <a href="{{ route('reports.supplies.fuel.pdf', ['start_date' => $start, 'end_date' => $end]) }}"
            class="btn btn-danger btn-sm" target="_blank">
            🧾 Export PDF
            </a>
        </div>
<br>
          <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>

    </div>
</x-app-layout>
