<x-app-layout>
    <x-slot name="header">Ajouter un transporteur</x-slot>
    <form action="{{ route('transporters.store') }}" method="POST">
        @include('transporters.form')
    </form>
</x-app-layout>