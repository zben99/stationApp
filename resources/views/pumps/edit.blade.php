<x-app-layout>
    <x-slot name="header">Modifier la Pompe</x-slot>

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
    <form method="POST" action="{{ route('pumps.update', $pump) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Nom de la pompe</label>
            <input type="text" name="name" class="form-control" value="{{ $pump->name }}" required>
        </div>



        <div class="mb-3">
            <label>Cuve associée</label>
            <select name="tank_id" class="form-control" required>
                @foreach($tanks as $tank)
                    <option value="{{ $tank->id }}" {{ $pump->tank_id == $tank->id ? 'selected' : '' }}>{{ $tank->code }} ({{ $tank->product->name }})</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
    </form>
</x-app-layout>
