<x-app-layout>
    <x-slot name="header">Nouvelle Réception de Carburant</x-slot>

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

    <form action="{{ route('fuel-receptions.store') }}" method="POST">
        @csrf

        <div class="row">


            <div class="col-md-4">
                <label>Date de réception</label>

                <input type="date" name="date_reception" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="rotation">Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Choisir une rotation --</option>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>


            <div class="col-md-4">
                <label>Numéro BL</label>
                <input type="text" name="num_bl" class="form-control">
            </div>

            {{-- Transporteur --}}
            <div class="col-md-4 mt-2">
                <label>Transporteur</label>
                <select id="transporterSelect"
                        name="transporter_id"        {{-- on garde ce name --}}
                        class="form-control select2-tag"
                        required>
                    <option value="">-- Sélectionner ou taper --</option>
                    @foreach ($transporters as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Chauffeur --}}
            <div class="col-md-4 mt-2">
                <label>Chauffeur</label>
                <select id="driverSelect"
                        name="driver_id"
                        class="form-control select2-tag"
                        required>
                    <option value="">-- Sélectionner ou taper --</option>
                    @foreach ($drivers as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-md-4 mt-2">
                <label>Immatriculation du véhicule</label>
                <input type="text"
                       name="vehicle_registration"
                       class="form-control"
                       placeholder="Ex. : 11-AA-1234"
                       value="{{ old('vehicle_registration') }}">
            </div>

            <div class="col-md-12 mt-3">
                <label>Commentaire</label>
                <textarea name="remarques" class="form-control"></textarea>
            </div>
        </div>

        <hr>
        <h5>Cuves concernées</h5>

        <table class="table table-bordered" id="cuvesTable">
            <thead>
                <tr>
                    <th>Cuve</th>
                    <th>Jauge Avant</th>
                    <th>Quantité Reçue</th>
                    <th>Jauge Après</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="tanks[0][tank_id]" class="form-control" required>
                            <option value="">-- Cuve --</option>
                            @foreach ($tanks as $tank)
                                <option value="{{ $tank->id }}">{{ $tank->code }} ( {{ $tank->product->name }} )</option>
                            @endforeach
                        </select>
                    </td>

                    <td><input type="number" step="0.01" name="tanks[0][jauge_avant]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="tanks[0][reception_par_cuve]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="tanks[0][jauge_apres]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-info" onclick="addRow()">+ Ajouter une cuve</button>

        <hr>
        <div class="d-flex justify-content-between">
            <a href="{{ route('fuel-receptions.index') }}" class="btn btn-secondary">Retour</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>

    <script>
        let index = 1;

        function addRow() {
            const row = `
            <tr>
                <td>
                    <select name="tanks[${index}][tank_id]" class="form-control" required>
                        <option value="">-- Cuve --</option>
                        @foreach ($tanks as $tank)
                            <option value="{{ $tank->id }}">{{ $tank->code }} ( {{ $tank->product->name }} )</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="tanks[${index}][jauge_avant]" class="form-control"></td>
                <td><input type="number" step="0.01" name="tanks[${index}][reception_par_cuve]" class="form-control"></td>
                <td><input type="number" step="0.01" name="tanks[${index}][jauge_apres]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
            </tr>`;
            $('#cuvesTable tbody').append(row);
            index++;
        }

        function removeRow(button) {
            $(button).closest('tr').remove();
        }
    </script>



<script>
document.addEventListener('DOMContentLoaded', () => {
    $('.select2-tag').select2({
        theme: 'bootstrap-5',       // optionnel si tu as bootstrap-select2
        tags: true,                 // autorise la saisie libre
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
