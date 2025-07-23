<x-app-layout>
    <x-slot name="header">Export Stock Carburant</x-slot>

    <form method="GET" action="{{ route('exports.fuel-stock.excel') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exporter
            </button>
        </div>
    </form>
</x-app-layout>
