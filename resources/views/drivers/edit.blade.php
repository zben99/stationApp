<x-app-layout>
    <x-slot name="title">Modifier un chauffeur</x-slot>
    <form action="{{ route('drivers.update', $driver) }}" method="POST">
        @method('PUT')
        @include('drivers.form', ['driver' => $driver])
    </form>
</x-app-layout>
