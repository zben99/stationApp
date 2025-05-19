<x-app-layout>
    <x-slot name="title">Valider une recette journalière</x-slot>


    @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
          <ul>
             @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
             @endforeach
          </ul>
        </div>
    @endifs
    <form action="{{ route('daily-revenue-review.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="date">Date :</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="rotation">Rotation :</label>
            <select name="rotation" class="form-control" required>
                <option value="6-14">6h - 14h</option>
                <option value="14-22">14h - 22h</option>
                <option value="22-6">22h - 6h</option>
            </select>
        </div>

        <hr>
        <h5>Récapitulatif des recettes</h5>

        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between">
                <span>Carburant</span>
                <strong>{{ number_format($fuelTotal, 0, ',', ' ') }} F</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Lubrifiants / PEA / Gaz / Lampes</span>
                <strong>{{ number_format($productTotal, 0, ',', ' ') }} F</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Boutique & Lavage</span>
                <strong>{{ number_format($shopTotal, 0, ',', ' ') }} F</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between bg-light">
                <strong>Total Général</strong>
                <strong>{{ number_format($fuelTotal + $productTotal + $shopTotal, 0, ',', ' ') }} F</strong>
            </li>
        </ul>

        <button type="submit" class="btn btn-success">Valider la revue</button>
    </form>
</x-app-layout>
