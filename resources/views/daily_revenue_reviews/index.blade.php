<x-app-layout>
    <x-slot name="title">Revue des recettes journalières</x-slot>

    <div class="mb-4">
        <a href="{{ route('daily-revenue-review.create') }}" class="btn btn-primary">Nouvelle validation</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Total Global</th>
                <th>Validé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($reviews as $review)
            <tr>
                <td>{{ $review->date }}</td>
                <td>{{ $review->rotation }}</td>
                <td>{{ number_format($review->total_amount, 0, ',', ' ') }} F</td>
                <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                <td>


                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-app-layout>
