<x-app-layout>
    <x-slot name="title">Paiements de la facture #{{ $invoice->invoice_number }}</x-slot>

    <h2>Paiements effectués pour la facture #{{ $invoice->invoice_number }}</h2>

    <!-- Table des paiements -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-4">
            <thead class="bg-light">
                <tr>
                    <th>Date</th>
                    <th>Rotation</th>
                    <th>Montant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->rotation }}</td>
                        <td>{{ number_format($payment->amount, 2) }} F</td>
                        <td>
                            <!-- Formulaire de suppression -->
                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $payment->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="confirmDelete(event, {{ $payment->id }})">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Informations sur le paiement total et montant restant -->
    <div class="mb-3">
        <p><strong>Total payé :</strong> {{ number_format($invoice->amount_paid, 2) }} F</p>
        <p><strong>Montant restant à payer :</strong> {{ number_format($invoice->amount_remaining, 2) }} F</p>
    </div>

    <!-- Bouton pour effectuer un nouveau paiement -->
    <a href="{{ route('payments.create', $invoice->id) }}" class="btn btn-success">Faire un paiement</a>
    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary mt-3">Retour à la liste des factures</a>

    <!-- Script de confirmation de suppression -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, paymentId) {
            event.preventDefault();  // Empêche l'envoi immédiat du formulaire

            // Affichage de SweetAlert pour confirmer la suppression
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                // Si la suppression est confirmée
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm' + paymentId); // Récupérer le formulaire par ID
                    if (form) {
                        form.submit();  // Soumettre le formulaire de suppression
                    } else {
                        console.error('Formulaire de suppression non trouvé'); // Affiche une erreur si le formulaire n'est pas trouvé
                    }
                }
            });
        }
    </script>
</x-app-layout>
