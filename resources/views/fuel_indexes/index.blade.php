<x-app-layout>
    <x-slot name="header">Relevés journaliers carburant</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('fuel-indexes.create') }}" class="btn btn-success">+ Nouveau relevé</a>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Nombre de pompes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $fuelIndexes->groupBy(fn($e) => $e->date->format('Y-m-d') . '|' . $e->rotation);
            @endphp

            @foreach($grouped as $key => $entries)
                @php [$date, $rotation] = explode('|', $key); @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                    <td>{{ $rotation }}</td>
                    <td>{{ $entries->count() }} pompes</td>
                    <td>
                        <a href="{{ route('fuel-indexes.details', ['date' => $date, 'rotation' => $rotation]) }}"
                           class="btn btn-sm btn-primary">
                            Voir détails
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
