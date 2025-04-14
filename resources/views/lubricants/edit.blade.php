<x-app-layout>
    <x-slot name="title">Modifier Réception - Lubrifiant</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('lubricant-receptions.update', $lubricantReception) }}" method="POST">
                @csrf
                @method('PUT')
                @include('lubricants._form')
            </form>
        </div>
    </div>
</x-app-layout>
