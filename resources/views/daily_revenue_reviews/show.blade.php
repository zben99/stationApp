<x-app-layout>
    <x-slot name="title">Détail de la revue du {{ $review->date }} - {{ $review->rotation }}</x-slot>

    <ul class="list-group mb-3">
        <li class="list-group-item d-flex justify-content-between">
            <span>Carburant</span>
            <strong>{{ number_format($review->fuel_amount, 0, ',', ' ') }} F</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Lubrifiants / PEA / Gaz / Lampes</span>
            <strong>{{ number_format($review->product_amount, 0, ',', ' ') }} F</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Boutique & Lavage</span>
            <strong>{{ number_format($review->shop_amount, 0, ',', ' ') }} F</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between bg-light">
            <strong>Total Général</strong>
            <strong>{{ number_format($review->total_amount, 0, ',', ' ') }} F</strong>
        </li>
    </ul>

    <a href="{{ route('daily-revenue-review.index') }}" class="btn btn-secondary">Retour</a>
</x-app-layout>
