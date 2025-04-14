<x-app-layout>
    <x-slot name="title">Modifier un Conditionnement</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('packagings.update', $packaging) }}" method="POST">
                @csrf
                @method('PUT')
                @include('packagings._form')
            </form>
        </div>
    </div>
</x-app-layout>
