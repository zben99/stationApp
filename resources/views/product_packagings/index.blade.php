<x-app-layout>
    <x-slot name="header">Conditionnements pour {{ $product->name }}</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('product-packagings.create', $product->id) }}" class="btn btn-primary mb-3">+ Associer un conditionnement</a>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Conditionnement</th>
                        <th>Unité</th>
                        <th>Prix d'achat</th>
                        <th>Prix de vente</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->productPackagings as $productPackaging)
                        <tr>
                            <td>{{ $productPackaging->packaging->label }}</td>
                            <td>{{ $productPackaging->packaging->unit }}</td>
                            <td>{{ number_format($productPackaging->prix_achat, 0, ',', ' ') }} F</td> <!-- Affichage du prix d'achat -->
                            <td>{{ number_format($productPackaging->price, 0, ',', ' ') }} F</td> <!-- Affichage du prix de vente -->
                            <td>{{ $productPackaging->stock }}</td>
                            <td>
                                <a href="{{ route('product-packagings.edit', [$product->id, $productPackaging->id]) }}" class="btn btn-sm btn-warning">Modifier</a>

                                <!-- Formulaire de suppression -->
                                <form action="{{ route('product-packagings.destroy', [$product->id, $productPackaging->id]) }}" method="POST" style="display:inline;" id="deleteForm{{ $productPackaging->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $productPackaging->id }})">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inclure SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(productPackagingId) {
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
                if (result.isConfirmed) {
                    // Soumettre le formulaire de suppression
                    document.getElementById('deleteForm' + productPackagingId).submit();
                }
            });
        }
    </script>

</x-app-layout>
