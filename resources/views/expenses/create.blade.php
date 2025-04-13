<x-app-layout>
    <x-slot name="title">Ajouter une Dépense</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @include('expenses._form')
            </form>
        </div>
    </div>
</x-app-layout>
