<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PagosExport implements FromCollection, WithHeadings
{
    protected $pagos;

    public function __construct($pagos)
    {
        $this->pagos = $pagos;
    }

    public function collection()
    {
        // Transformamos los datos a un array plano para Excel
        return $this->pagos->map(function($pago){
            return [
                'Cliente' => $pago->contrato->cliente->nombres_completos ?? '-',
                'DNI' => $pago->contrato->cliente->dni ?? '-',
                'Categoría' => $pago->contrato->puesto->categoria->nombre ?? '-',
                'N° Puesto' => $pago->contrato->puesto->numero_puesto ?? '-',
                'Monto' => $pago->monto,
                'Estado' => $pago->estado,
                'Accesor' => $pago->accesor->nombres_completos ?? '-',
                'Fecha a Pagar' => $pago->fecha_a_pagar,
                'Fecha de Pago' => $pago->fecha_pago ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'DNI',
            'Categoría',
            'N° Puesto',
            'Monto',
            'Estado',
            'Accesor',
            'Fecha a Pagar',
            'Fecha de Pago',
        ];
    }
}
