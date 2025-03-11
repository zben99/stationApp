<x-app-layout>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Créer une nouvelle station </h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-danger btn-sm mb-2" href="{{ route('stations.index') }}"><i class="fa fa-arrow-left"></i> Retour</a>
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

<form method="POST" action="{{ route('stations.store') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Référence :</strong>
                <input type="text" name="name" placeholder="Référence" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Adresse :</strong>
                <input type="text" name="location" placeholder="Adresse" class="form-control">
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Statut:</strong>
                <select name="is_active" class="form-control">
                        <option value=""> </option>
                        <option value="1">Activé </option>
                        <option value="0">Désactivé</option>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 ">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Soumettre</button>
        </div>
    </div>
</form>
</x-app-layout>
