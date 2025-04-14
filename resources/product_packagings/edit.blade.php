<x-app-layout>
    <x-slot name="title">Modifier Conditionnement</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('product-packagings.update', $productPackaging) }}" method="POST">
                @csrf
                @method('PUT')
                @include('product_packagings._form')
            </form>
        </div>
    </div>
</x-app-layout>
