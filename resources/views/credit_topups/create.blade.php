<x-app-layout>
    <x-slot name="title">Nouvelle recharge de crédit</x-slot>

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

    <form action="{{ route('credit-topups.store') }}" method="POST">
        @csrf

            <div class="mb-3">
                <label  for="client_id" class="form-label">Client</label>
                <select id="clientSelect"
                        name="client_id"
                        class="form-control select2-tag"
                        required>
                    <option value="">-- Sélectionner ou taper --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

        <div class="mb-3">
            <label for="amount">Montant (F CFA)</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="rotation">Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="6-14" {{ old('rotation', $creditTopup->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ old('rotation', $creditTopup->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ old('rotation', $creditTopup->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes">Notes (facultatif)</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('credit-topups.index') }}" class="btn btn-secondary">Annuler</a>
    </form>



        <script>
        document.addEventListener('DOMContentLoaded', () => {
            $('.select2-tag').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: '-- Sélectionner ou taper --',
                width: '100%',
                language: {
                    noResults: () => 'Aucun résultat',
                    inputTooShort: () => 'Tape au moins 1 caractère'
                }
            });
        });
    </script>
</x-app-layout>
