<x-app-layout>
    <x-slot name="header">Relevé journalier par pompe</x-slot>

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

    <form method="POST" action="{{ route('fuel-indexes.store') }}" id="fuel-form">
        @csrf

        <div class="row mb-4">
            <div class="col-md-4">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label>Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Choisir --</option>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>
        </div>

        <hr>

        <div id="pump-lines">
            <div class="row align-items-end mb-3 pump-line">
                <div class="col-md-2">
                    <label>Type</label>
                    <select class="form-control type-select" required>
                        <option value="">-- Type --</option>
                        <option value="super">SUPER</option>
                        <option value="gazoil">GAZOIL</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>POMPE</label>
                    <select name="pumps[0][pump_id]" class="form-control pump-select" required>
                        <option value="">-- Choisir --</option>
                        @foreach($pumps as $pump)
                            @php $type = str_contains(strtolower($pump->tank->product->name), 'super') ? 'super' : 'gazoil'; @endphp
                            <option value="{{ $pump->id }}"
                                    data-type="{{ $type }}"
                                    data-label="{{ $pump->name }}"
                                    data-last="{{ $lastIndexes[$pump->id] ?? 0 }}"
                                    data-price="{{ $pump->tank->product->price }}">
                                {{ strtoupper($type) }} - {{ $pump->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="pumps[0][prix_unitaire]" class="prix-unitaire">
                </div>

                <div class="col-md-2">
                    <label>Index début</label>
                    <input type="number" step="0.01" name="pumps[0][index_debut]" class="form-control index-debut" readonly>
                </div>

                <div class="col-md-2">
                    <label>Index fin</label>
                    <input type="number" step="0.01" name="pumps[0][index_fin]" class="form-control index-fin" required>
                </div>

                <div class="col-md-2">
                    <label>Retour cuve</label>
                    <input type="number" step="0.01" name="pumps[0][retour_en_cuve]" class="form-control retour-cuve">
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-line mt-4">🗑️</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-line" class="btn btn-outline-primary btn-sm">+ Ajouter une pompe</button>

        <div class="mt-4">
            <h5>Total global : <span id="grand-total">0 FCFA</span></h5>
        </div>

        <button type="submit" class="btn btn-success mt-3">✅ Enregistrer</button>
    </form>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let lineIndex = 1;

        function updatePumpOptions(line) {
            const type = line.querySelector('.type-select').value;
            const pumpSelect = line.querySelector('.pump-select');
            const selectedIds = Array.from(document.querySelectorAll('.pump-select'))
                .map(sel => sel.value)
                .filter(val => val !== pumpSelect.value && val !== "");

            Array.from(pumpSelect.options).forEach(opt => {
                const dataType = opt.dataset.type;
                if (!opt.value) return opt.hidden = false;
                opt.hidden = (dataType !== type) || selectedIds.includes(opt.value);
            });

            pumpSelect.value = "";
            line.querySelector('.index-debut').value = "";
            line.querySelector('.prix-unitaire').value = "";
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.pump-line').forEach(line => {
                const debut = parseFloat(line.querySelector('.index-debut').value) || 0;
                const fin = parseFloat(line.querySelector('.index-fin').value) || 0;
                const retour = parseFloat(line.querySelector('.retour-cuve').value) || 0;
                const selected = line.querySelector('.pump-select').selectedOptions[0];
                const price = parseFloat(selected?.dataset.price || 0);
                const vol = Math.max(fin - debut - retour, 0);
                total += vol * price;
            });
            document.getElementById('grand-total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
        }

        document.getElementById('add-line').addEventListener('click', () => {
            const original = document.querySelector('.pump-line');
            const clone = original.cloneNode(true);

            clone.querySelectorAll('input, select').forEach(el => {
                el.name = el.name?.replace(/\[\d+\]/, `[${lineIndex}]`) || "";
                if (el.tagName === 'SELECT') el.value = '';
                else el.value = '';
            });

            document.getElementById('pump-lines').appendChild(clone);
            lineIndex++;
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('type-select')) {
                const line = e.target.closest('.pump-line');
                updatePumpOptions(line);
            }

            if (e.target.classList.contains('pump-select')) {
                const selected = e.target.selectedOptions[0];
                const line = e.target.closest('.pump-line');
                line.querySelector('.index-debut').value = selected.dataset.last || '';
                line.querySelector('.prix-unitaire').value = selected.dataset.price || '';
                updateGrandTotal();
            }
        });

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('index-fin') || e.target.classList.contains('retour-cuve')) {
                updateGrandTotal();
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-line')) {
                const lines = document.querySelectorAll('.pump-line');
                if (lines.length > 1) {
                    e.target.closest('.pump-line').remove();
                    updateGrandTotal();
                }
            }
        });

        document.getElementById('fuel-form').addEventListener('submit', function (e) {
            const totalPompes = {{ count($pumps) }};
            const selectedPompes = Array.from(document.querySelectorAll('.pump-select'))
                .map(sel => sel.value)
                .filter(val => val !== '');

            const uniquePompes = [...new Set(selectedPompes)];

            if (uniquePompes.length < totalPompes) {
                e.preventDefault();
                Swal.fire({
                    title: "Toutes les pompes ne sont pas renseignées",
                    text: "Voulez-vous quand même enregistrer ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Oui, continuer",
                    cancelButtonText: "Annuler"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('fuel-form').submit();
                    }
                });
            }
        });
    </script>
</x-app-layout>
