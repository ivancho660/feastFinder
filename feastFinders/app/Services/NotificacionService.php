<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Events\NotificacionOrdenPagada;

class NotificacionService
{
    protected $twilioSid;
    protected $twilioToken;
    protected $twilioNumber;

    public function __construct()
    {
        $this->twilioSid = config('services.twilio.sid');
        $this->twilioToken = config('services.twilio.token');
        $this->twilioNumber = config('services.twilio.number');
    }

    /**
     * Envía un SMS usando Twilio
     */
    public function enviarSMS(string $telefono, string $mensaje): void
    {
        try {
            if (!str_starts_with($telefono, '+')) {
                $telefono = '+57' . $telefono;
            }

            $twilio = new Client($this->twilioSid, $this->twilioToken);

            $message = $twilio->messages->create(
                $telefono,
                [
                    'from' => $this->twilioNumber,
                    'body' => $mensaje
                ]
            );

            Log::info("Mensaje enviado a: " . $telefono . " - SID: " . $message->sid);

        } catch (\Exception $e) {
            Log::error("Error al enviar SMS: " . $e->getMessage());
        }
    }

    /**
     * Envía notificación de orden pagada
     */
    public function enviarNotificacionOrdenPagada(int $adminId, int $ordenId, string $numeroOrden, int $restauranteId): void
    {
        try {
            $mensaje = [
                'tipo' => 'orden_pagada',
                'ordenId' => $ordenId,
                'numeroOrden' => $numeroOrden,
                'restauranteId' => $restauranteId,
                'mensaje' => 'Nueva orden #' . $numeroOrden . ' en tu restaurante',
                'timestamp' => now()->timestamp
            ];

            // Dispara evento para notificación en tiempo real (usando Laravel Echo y WebSockets)
            event(new NotificacionOrdenPagada($adminId, $mensaje));

            Log::info("Notificación enviada al admin $adminId para orden $numeroOrden");

        } catch (\Exception $e) {
            Log::error("Error al enviar notificación: " . $e->getMessage());
        }
    }
}