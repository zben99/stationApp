<x-app-layout>
    {{-- Titre + retour --}}
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Modifier le produit</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-sm mb-2"
                   href="{{ route('lubricant-products.index') }}">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    {{-- Erreurs --}}
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
    <form action="{{ route('lubricant-products.update', $product->id) }}"
          method="POST">
        @csrf
        @method('PUT')

        {{-- Code produit --}}
        <div class="mb-3">
            <label class="form-label"><strong>Code produit :</strong></label>
            <input type="text"
                   name="code"
                   class="form-control"
                   maxlength="30"
                   value="{{ old('code', $product->code) }}"
                   required>
        </div>

        {{-- Produit --}}
        <div class="mb-3">
            <label class="form-label"><strong>Produit :</strong></label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $product->name) }}"
                   required>
        </div>

        {{-- Catégorie --}}
        <div class="mb-3">
            <label class="form-label"><strong>Catégorie :</strong></label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Choisir --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
