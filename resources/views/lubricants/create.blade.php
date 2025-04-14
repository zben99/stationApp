<x-app-layout>
    <x-slot name="title">Nouvelle Réception - Lubrifiant</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('lubricant-receptions.store') }}" method="POST">
                @include('lubricants._form')
            </form>
        </div>
    </div>
</x-app-layout>
