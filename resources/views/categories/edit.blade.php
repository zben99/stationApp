<x-app-layout>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Modifier la catégorie</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-sm mb-2" href="{{ route('categories.index') }}">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('categories.update', $categorie->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Catégorie :</strong>
                    <input type="text" name="name" value="{{ old('name', $categorie->name) }}" placeholder="Catégorie" class="form-control">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <strong>Type :</strong>
                <select name="type" class="form-control">
                    <option value=""></option>
                    <option value="fuel" {{ $categorie->type=='fuel' ? 'selected' : '' }}>Fuel</option>
                    <option value="lubrifiant" {{ $categorie->type=='lubrifiant' ? 'selected' : '' }}>Lubrifiant</option>
                    <option value="boutique" {{ $categorie->type=='boutique' ? 'selected' : '' }}>Boutique</option>

                </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Statut :</strong>
                    <select name="is_active" class="form-control">
                        <option value="">Sélectionner</option>
                        <option value="1" {{ $categorie->is_active ? 'selected' : '' }}>Activé   </option>
                        <option value="0" {{ !$categorie->is_active ? 'selected' : '' }}>Désactivé</option>
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Mettre à jour
                </button>
            </div>
        </div>
    </form>
    </x-app-layout>
