<table>
    <tr><th colspan="2">Fiche de Dépotage</th></tr>
    <tr><td>Date</td><td>{{ \Carbon\Carbon::parse($reception->date_reception)->format('d/m/Y') }}</td></tr>
    <tr><td>Station</td><td>{{ $reception->station->name ?? '-' }} {{ $reception->station->location ?? '-' }}</td></tr>
    <tr><td>Transporteur</td><td>{{ $reception->transporter->name ?? '-' }}</td></tr>
    <tr><td>Chauffeur</td><td>{{ $reception->driver->name ?? '-' }}</td></tr>
    <tr><td>BL</td><td>{{ $reception->num_bl ?? '-' }}</td></tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>Cuve</th>
            <th>Produit</th>
            <th>Jauge Avant</th>
            <th>Quantité Reçue</th>
            <th>Jauge Après</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reception->lines as $line)
        <tr>
            <td>{{ $line->tank->code ?? '-' }}</td>
            <td>{{ $line->tank->product->name ?? '-' }}</td>
            <td>{{ $line->jauge_avant }}</td>
            <td>{{ $line->reception_par_cuve }}</td>
            <td>{{ $line->jauge_apres }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
