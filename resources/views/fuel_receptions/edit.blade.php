<x-app-layout>
    <x-slot name="header">Modifier la Réception de Carburant</x-slot>

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

    <form action="{{ route('fuel-receptions.update', $reception->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <label>Date de réception</label>
                <input type="date" name="date_reception" class="form-control" value="{{ \Carbon\Carbon::parse($reception->date_reception)->format('Y-m-d') }}" required>
            </div>

            <div class="col-md-6">
                <label>Numéro BL</label>
                <input type="text" name="num_bl" class="form-control" value="{{ $reception->num_bl }}">
            </div>

            <div class="col-md-6 mt-2">
                <label>Transporteur</label>
                <select name="transporter_id" class="form-control">
                    <option value="">-- Sélectionner --</option>
                    @foreach ($transporters as $t)
                        <option value="{{ $t->id }}" {{ $reception->transporter_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mt-2">
                <label>Chauffeur</label>
                <select name="driver_id" class="form-control">
                    <option value="">-- Sélectionner --</option>
                    @foreach ($drivers as $d)
                        <option value="{{ $d->id }}" {{ $reception->driver_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mt-3">
                <label>Commentaire</label>
                <textarea name="remarques" class="form-control">{{ $reception->remarques }}</textarea>
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
                @foreach ($reception->lines as $index => $line)
                    <tr>
                        <td>
                            <select name="tanks[{{ $index }}][tank_id]" class="form-control" required>
                                <option value="">-- Cuve --</option>
                                @foreach ($tanks as $tank)
                                    <option value="{{ $tank->id }}" {{ $line->tank_id == $tank->id ? 'selected' : '' }}>{{ $tank->code }} ({{ $tank->product->name }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="tanks[{{ $index }}][jauge_avant]" value="{{ $line->jauge_avant }}" class="form-control"></td>
                        <td><input type="number" step="0.01" name="tanks[{{ $index }}][reception_par_cuve]" value="{{ $line->reception_par_cuve }}" class="form-control"></td>
                        <td><input type="number" step="0.01" name="tanks[{{ $index }}][jauge_apres]" value="{{ $line->jauge_apres }}" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-info" onclick="addRow()">+ Ajouter une cuve</button>

        <hr>
        <div class="d-flex justify-content-between">
            <a href="{{ route('fuel-receptions.index') }}" class="btn btn-secondary">Retour</a>
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </div>
    </form>

    <script>
        let index = {{ $reception->lines->count() }};

        function addRow() {
            const row = `
                <tr>
                    <td>
                        <select name="tanks[${index}][tank_id]" class="form-control" required>
                            <option value="">-- Cuve --</option>
                            @foreach ($tanks as $tank)
                                <option value="{{ $tank->id }}">{{ $tank->code }} ({{ $tank->product->name }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="tanks[${index}][jauge_avant]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="tanks[${index}][reception_par_cuve]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="tanks[${index}][jauge_apres]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
                </tr>`;

            document.querySelector('#cuvesTable tbody').insertAdjacentHTML('beforeend', row);
            index++;
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }
    </script>
</x-app-layout>
