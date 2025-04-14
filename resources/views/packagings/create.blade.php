<x-app-layout>
    <x-slot name="title">Ajouter un Conditionnement</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('packagings.store') }}" method="POST">
                @include('packagings._form')
            </form>
        </div>
    </div>
</x-app-layout>
