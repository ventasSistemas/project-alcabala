<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Caja;

class CajaCerradaNotification extends Notification
{
    use Queueable;
    protected $caja;

    public function __construct(Caja $caja)
    {
        $this->caja = $caja;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Informe de Cierre de Caja')
            ->greeting('Hola Administrador,')
            ->line("El accesor **{$this->caja->accesor->nombres}** ha cerrado su caja.")
            ->line("📅 Fecha de apertura: {$this->caja->fecha_apertura}")
            ->line("🕒 Fecha de cierre: {$this->caja->fecha_cierre}")
            ->line("💰 Total ingresos: S/. {$this->caja->total_ingresos}")
            ->line("💸 Total egresos: S/. {$this->caja->total_egresos}")
            ->line("Saldo final: S/. {$this->caja->saldo_final}")
            ->salutation('Sistema Feria Municipal');
    }
}