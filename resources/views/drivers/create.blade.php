<x-app-layout>
    <x-slot name="title">Ajouter un chauffeur</x-slot>
    <form action="{{ route('drivers.store') }}" method="POST">
        @include('drivers.form')
    </form>
</x-app-layout>
