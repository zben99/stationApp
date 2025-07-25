<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConsolidatedRevenueExport implements FromArray, WithTitle, WithHeadings
{
    protected $validation;
    protected $date;
    protected $rotation;

    public function __construct($validation, $date, $rotation)
    {
        $this->validation = $validation;
        $this->date = $date;
        $this->rotation = $rotation;
    }

    public function array(): array
    {
        $v = $this->validation;

        return [
            ['--- Carburants ---', ''],
            ['Carburant Super', $v->fuel_super_amount],
            ['Carburant Gasoil', $v->fuel_gazoil_amount],

            ['--- Produits vendus ---', ''],
            ['Lubrifiants', $v->lub_amount],
            ['PEA', $v->pea_amount],
            ['Gaz', $v->gaz_amount],
            ['Lampes', $v->lampes_amount],
            ['Divers', $v->divers_amount],
            ['Lavage', $v->lavage_amount],
            ['Boutique', $v->boutique_amount],

            ['--- Crédits & Soldes ---', ''],
            ['Crédit reçu', $v->credit_received],
            ['Remboursement crédit', $v->credit_repaid],
            ['Recharge solde', $v->balance_received],
            ['Solde utilisé', $v->balance_used],

            ['--- Sorties ---', ''],
            ['Dépenses', $v->expenses],
            ['Factures payées', $v->payment_facture],

            ['--- Résumé ---', ''],
            ['Montant en caisse', $v->cash_amount],
            ['Écart caisse / net', $v->cash_amount - $v->net_to_deposit],
            ['Montant net à déposer', $v->net_to_deposit],
        ];
    }

    public function headings(): array
    {
        return ["Catégorie", "Montant (FCFA)"];
    }

    public function title(): string
    {
        return "Recette $this->date $this->rotation";
    }
}
