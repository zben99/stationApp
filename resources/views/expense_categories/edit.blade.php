<x-app-layout>
    <x-slot name="header">Modifier Rubrique</x-slot>


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

    <form method="POST" action="{{ route('expense-categories.update', $expenseCategory->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nom</label>
                <input type="text" name="name" class="form-control" value="{{ $expenseCategory->name }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Description</label>
                <input type="text" name="description" class="form-control" value="{{ $expenseCategory->description }}">
            </div>

            <div class="col-12 mb-3">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ $expenseCategory->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Actif</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </div>
    </form>
</x-app-layout>
