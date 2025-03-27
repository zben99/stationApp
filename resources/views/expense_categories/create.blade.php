<x-app-layout>
    <x-slot name="header">Nouvelle Rubrique</x-slot>


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
    <form method="POST" action="{{ route('expense-categories.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nom</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Description</label>
                <input type="text" name="description" class="form-control">
            </div>

            <div class="col-12 mb-3">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                    <label class="form-check-label">Actif</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </form>
</x-app-layout>
