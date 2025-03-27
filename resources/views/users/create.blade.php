<x-app-layout>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Créer un nouvel utilisateur</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-danger btn-sm mb-2" href="{{ route('users.index') }}"><i class="fa fa-arrow-left"></i> Retour</a>
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

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nom complet:</strong>
                <input type="text" name="name" placeholder="Nom complet" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Compte:</strong>
                <input type="text" name="email" placeholder="Compte" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Mot de passe:</strong>
                <input type="password" name="password" placeholder="Mot de passe" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Confirmez le mot de passe:</strong>
                <input type="password" name="password_confirmation" placeholder="Confirmez le mot de passe" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Rôle:</strong>
                <select name="roles[]" class="form-control">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                     @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 ">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Soumettre</button>
        </div>
    </div>
</form>
</x-app-layout>
