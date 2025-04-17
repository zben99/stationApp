<x-app-layout>
    <x-slot name="title">Associer un Conditionnement</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('product-packagings.store') }}" method="POST">
                <input type="hidden" name="station_product_id" value="{{ $product->id }}">
                @include('product_packagings._form', ['availablePackagings' => $availablePackagings])
            </form>
        </div>
    </div>
</x-app-layout>
