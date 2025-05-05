<x-app-layout>
    <x-slot name="header">Nouvelle Pompe</x-slot>

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

    <form method="POST" action="{{ route('pumps.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nom de la pompe</label>
            <input type="text" name="name" class="form-control" required>
        </div>



        <div class="mb-3">
            <label>Cuve associée</label>
            <select name="tank_id" class="form-control" required>
                @foreach($tanks as $tank)
                    <option value="{{ $tank->id }}">{{ $tank->code }} ({{ $tank->product->name }})</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Enregistrer</button>
    </form>
</x-app-layout>
