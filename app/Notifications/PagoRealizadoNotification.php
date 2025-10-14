<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pago;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PagoRealizadoNotification extends Notification
{
    use Queueable;

    public $pago;

    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'pago_id' => $this->pago->id,
            'titulo' => 'Pago Registrado Correctamente',
            'mensaje' => 'Se realizó el pago N° ' . $this->pago->numero_pago . 
                        ' por S/ ' . number_format($this->pago->monto, 2),
            'fecha' => now()->format('d/m/Y H:i'),
        ];
    }
}
