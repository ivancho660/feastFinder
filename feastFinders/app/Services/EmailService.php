<?php

namespace App\Services;

use App\Mail\MensajeSimpleMail; // Asegúrate de importar tu Mailable
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Envía un correo electrónico con una plantilla Blade.
     *
     * @param string $destinatario El email del receptor.
     * @param string $asunto El asunto del correo.
     * @param string $codigo El código de restablecimiento de contraseña.
     * @param int $expiracionHoras Las horas de validez del código.
     * @return void
     */
    public function enviarCorreo($destinatario, $asunto, $codigo, $expiracionHoras)
    {
        // Prepara los datos que se enviarán a la vista Blade
        $datosParaVista = [
            'codigo' => $codigo,
            'expiracion' => $expiracionHoras,
        ];

        // Crea una instancia de tu Mailable, pasándole el asunto y los datos.
        $emailMailable = new MensajeSimpleMail($asunto, $datosParaVista);

        // Envía el correo al destinatario
        Mail::to($destinatario)->send($emailMailable);
    }
}