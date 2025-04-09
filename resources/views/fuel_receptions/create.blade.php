<x-app-layout>
    <x-slot name="title">Nouvelle Réception de Carburant</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('fuel-receptions.store') }}" method="POST">
                @include('fuel_receptions._form')
            </form>
        </div>
    </div>
</x-app-layout>
