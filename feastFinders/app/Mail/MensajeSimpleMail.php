<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MensajeSimpleMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El asunto del correo electrónico.
     * @var string
     */
    public $asunto;

    /**
     * Un arreglo asociativo que contiene los datos que se pasarán a la vista Blade.
     * Por ejemplo: ['codigo' => 'ABC123', 'expiracion' => 2].
     * @var array
     */
    public $datos;

    /**
     * Crea una nueva instancia del mensaje.
     *
     * @param string $asunto El asunto del correo.
     * @param array $datos Un arreglo de datos que se usarán en la vista del correo.
     */
    public function __construct(string $asunto, array $datos)
    {
        $this->asunto = $asunto;
        $this->datos = $datos;
    }

    /**
     * Obtiene el sobre del mensaje.
     * Define el asunto del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     * Especifica la vista Blade a usar para el cuerpo del correo
     * y los datos que se le pasarán.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            // La vista 'emails.codigo_restablecimiento' se refiere a
            // resources/views/emails/codigo_restablecimiento.blade.php
            view: 'emails.codigo_restablecimiento',
            // Todas las claves del arreglo $this->datos estarán disponibles
            // como variables ($codigo, $expiracion, etc.) dentro de esa vista Blade.
            with: $this->datos,
        );
    }

    /**
     * Obtiene los adjuntos para el mensaje.
     * Actualmente, no se adjunta ningún archivo.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}