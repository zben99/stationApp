<x-app-layout>
    {{-- Titre + retour --}}
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ isset($product) ? 'Modifier un produit' : 'Créer un produit' }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-sm mb-2"
                   href="{{ route('lubricant-products.index') }}">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    {{-- Affichage des erreurs --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops !</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulaire --}}
    <form action="{{ isset($product)
                     ? route('lubricant-products.update', $product->id)
                     : route('lubricant-products.store') }}"
          method="POST">
        @csrf
        @isset($product)
            @method('PUT')
        @endisset

          {{-- Code produit --}}
        <div class="mb-3">
            <label class="form-label"><strong>Code produit :</strong></label>
            <input type="text"
                   name="code"
                   value="{{ old('code', $product->code ?? '') }}"
                   class="form-control"
                   maxlength="30"
                   required>
        </div>

        {{-- Nom du produit --}}
        <div class="mb-3">
            <label class="form-label"><strong>Produit :</strong></label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $product->name ?? '') }}"
                   class="form-control"
                   required>
        </div>

        {{-- Catégorie --}}
        <div class="mb-3">
            <label class="form-label"><strong>Catégorie :</strong></label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Choisir --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>



        {{-- Bouton submit --}}
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Soumettre
        </button>
    </form>
</x-app-layout>
