<x-app-layout>
    <x-slot name="title">Modifier la Réception</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('fuel-receptions.update', $fuelReception) }}" method="POST">
                @csrf
                @method('PUT')
                @include('fuel_receptions._form')
            </form>
        </div>
    </div>
</x-app-layout>
