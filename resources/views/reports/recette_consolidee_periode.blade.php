<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">Recettes consolidées par période</h2>
    </x-slot>

    <div class="container py-4">
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="date" name="start_date" value="{{ $start }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <input type="date" name="end_date" value="{{ $end }}" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary w-100">Afficher</button>


            </div>

        </form>

        @if(count($validations))
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Rotation</th>
                            <th>Super</th>
                            <th>Gasoil</th>
                            <th>Lub</th>
                            <th>PEA</th>
                            <th>Gaz</th>
                            <th>Boutique</th>
                            <th>Crédit</th>
                            <th>Solde</th>
                            <th>Dépenses</th>
                            <th>Facture</th>
                            <th>Net à déposer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validations as $v)
                            <tr>
                                <td>{{ $v->date }}</td>
                                <td>{{ $v->rotation }}</td>
                                <td>{{ number_format($v->fuel_super_amount, 0) }}</td>
                                <td>{{ number_format($v->fuel_gazoil_amount, 0) }}</td>
                                <td>{{ number_format($v->lub_amount, 0) }}</td>
                                <td>{{ number_format($v->pea_amount, 0) }}</td>
                                <td>{{ number_format($v->gaz_amount, 0) }}</td>
                                <td>{{ number_format($v->boutique_amount, 0) }}</td>
                                <td>{{ number_format($v->credit_received, 0) }}</td>
                                <td>{{ number_format($v->balance_received, 0) }}</td>
                                <td>{{ number_format($v->expenses, 0) }}</td>
                                <td>{{ number_format($v->payment_facture, 0) }}</td>
                                <td><strong>{{ number_format($v->net_to_deposit, 0) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($start && $end)
            <div class="alert alert-warning">Aucune recette trouvée entre ces dates.</div>
        @endif

          @if($start && $end)
                    <div class="mb-3 d-flex gap-2">
                            <a href="{{ route('reports.consolidee.period.pdf', ['start_date' => $start, 'end_date' => $end]) }}" target="_blank" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>

                            <a href="{{ route('reports.consolidee.period.excel', ['start_date' => $start, 'end_date' => $end]) }}" target="_blank" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                </div>
              @endif

              <br>
                <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>

    </div>

</x-app-layout>
