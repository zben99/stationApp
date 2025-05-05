<x-app-layout>
    <x-slot name="title">Modifier une Dépense</x-slot>

    <div class="card">
        <div class="card-body">

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
            <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('expenses._form')
            </form>
        </div>
    </div>
</x-app-layout>
