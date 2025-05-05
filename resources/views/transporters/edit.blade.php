<x-app-layout>
    <x-slot name="header">Modifier le transporteur</x-slot>

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

    <form action="{{ route('transporters.update', $transporter) }}" method="POST">
        @method('PUT')
        @include('transporters.form', ['transporter' => $transporter])
    </form>
</x-app-layout>
