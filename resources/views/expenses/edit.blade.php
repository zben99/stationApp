<x-app-layout>
    <x-slot name="title">Modifier une Dépense</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('expenses._form')
            </form>
        </div>
    </div>
</x-app-layout>
