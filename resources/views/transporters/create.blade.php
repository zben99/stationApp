<x-app-layout>
    <x-slot name="header">Ajouter un transporteur</x-slot>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
        <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        </div>
    @endif
    <form action="{{ route('transporters.store') }}" method="POST">
        @include('transporters.form')
    </form>
</x-app-layout>
