<x-app-layout>
    <x-slot name="title">Modifier Conditionnement</x-slot>

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
            <form action="{{ route('product-packagings.update', $productPackaging) }}" method="POST">
                @csrf
                @method('PUT')
                @include('product_packagings._form')
            </form>
        </div>
    </div>
</x-app-layout>
