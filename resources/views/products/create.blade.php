
<x-app-layout>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Créer une catégorie </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-sm mb-2" href="{{ route('categories.index') }}"><i class="fa fa-arrow-left"></i> Retour</a>
            </div>
        </div>
    </div>

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

    <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif




        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Produit :</strong>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"  class="form-control" required>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Catégorie :</strong>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ isset($product) && $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Station :</strong>
                <select name="station_id" class="form-control" required>
                    @foreach($stations as $station)
                        <option value="{{ $station->id }}" {{ isset($product) && $product->station_id == $station->id ? 'selected' : '' }}>
                            {{ $station->name }}  {{ $station->location }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Stock initial :</strong>
                <input type="number" step="0.01" name="stock" value="{{ old('stock', $product->stock ?? '') }}" class="form-control" required>
            </div>
        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Prix :</strong>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" class="form-control" required>
            </div>
        </div>




        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Soumettre</button>

    </form>

    </x-app-layout>