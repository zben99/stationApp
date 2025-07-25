<x-app-layout>
    <x-slot name="header">Contrôle Stock Carburant</x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('exports.fuel-stock.excel') }}" class="row g-3 mb-4">
        @csrf

        <div class="col-md-3">
            <label for="from" class="form-label">Date début</label>
            <input type="date" name="from" id="from" value="{{ old('from', $from) }}" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label for="to" class="form-label">Date fin</label>
            <input type="date" name="to" id="to" value="{{ old('to', $to) }}" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label for="force" class="form-label">Options</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="force" value="1" id="force" {{ old('force') ? 'checked' : '' }}>
                <label class="form-check-label" for="force">
                    Forcer le recalcul
                </label>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-success w-100">
                <i class="fas fa-file-excel"></i> Exporter
            </button>
        </div>

        <div class="col-12">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Cuve</th>
                        <th>Stock physique (litres)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tanks as $tank)
                        <tr>
                            <td>{{ $tank->code }} ({{ $tank->product->name }})</td>
                            <td>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="stocks[{{ $tank->id }}]"
                                    class="form-control"
                                    value="{{ old('stocks.'.$tank->id, $tank->physical_stock_value) }}"
                                >
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>


        <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>


</x-app-layout>
