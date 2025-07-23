<x-app-layout>
    <x-slot name="header">Export Stock Carburant</x-slot>

    <form method="POST" action="{{ route('exports.fuel-stock.excel') }}" class="row g-3 mb-4">
        @csrf
        <div class="col-md-3">
            <input type="date" name="from" value="{{ old('from', $from) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="to" value="{{ old('to', $to) }}" class="form-control" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exporter
            </button>
        </div>

        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cuve</th>
                        <th>Stock physique</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tanks as $tank)
                        <tr>
                            <td>{{ $tank->code }}</td>
                            <td>
                                <input type="number" step="0.01" name="stocks[{{ $tank->id }}]" class="form-control" value="{{ old('stocks.'.$tank->id, $tank->physical_stock_value) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</x-app-layout>
