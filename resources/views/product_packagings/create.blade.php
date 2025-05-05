<x-app-layout>
    <x-slot name="title">Associer un Conditionnement</x-slot>

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
            <form action="{{ route('product-packagings.store') }}" method="POST">
                <input type="hidden" name="station_product_id" value="{{ $product->id }}">
                @include('product_packagings._form', ['availablePackagings' => $availablePackagings])
            </form>
        </div>
    </div>
</x-app-layout>
