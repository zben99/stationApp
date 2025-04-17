<x-app-layout>
    <x-slot name="header">Modifier le transporteur</x-slot>
    <form action="{{ route('transporters.update', $transporter) }}" method="POST">
        @method('PUT')
        @include('transporters.form', ['transporter' => $transporter])
    </form>
</x-app-layout>