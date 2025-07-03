<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $stripeSecretKey;
    protected $successUrl;
    protected $cancelUrl;

    public function __construct()
    {
        $this->stripeSecretKey = config('services.stripe.secret');
        $this->successUrl = config('services.stripe.success_url');
        $this->cancelUrl = config('services.stripe.cancel_url');
        
        Stripe::setApiKey($this->stripeSecretKey);
        Log::info('StripeService inicializado con clave: ' . substr($this->stripeSecretKey, 0, 8) . '...');
    }

    public function createCheckoutSession(string $nombre, string $descripcion, float $total, 
                                        array $metadata, string $successUrl = null, string $cancelUrl = null): array
    {
        // 1. Validación avanzada de parámetros
        if ($total <= 0) {
            throw new \InvalidArgumentException('El total debe ser mayor a cero');
        }
        if (empty($nombre)) {
            throw new \InvalidArgumentException('El nombre no puede estar vacío');
        }
        if (!isset($metadata['orden_id'])) {
            throw new \InvalidArgumentException('Metadata debe incluir orden_id');
        }

        // 2. Conversión a centavos con validación
        $amount = $total * 100;
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Monto total inválido: ' . $total);
        }

        try {
            // 3. Construcción de URLs con parámetros
            $successUrlWithParams = ($successUrl ?: $this->successUrl) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrlWithParams = ($cancelUrl ?: $this->cancelUrl) . '?session_id={CHECKOUT_SESSION_ID}';

            Log::info("Success URL construida: " . $successUrlWithParams);
            Log::info("Cancel URL construida: " . $cancelUrlWithParams);

            // 4. Creación de la sesión
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'cop',
                        'product_data' => [
                            'name' => $nombre,
                            'description' => $descripcion,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => $metadata,
                'success_url' => $successUrlWithParams,
                'cancel_url' => $cancelUrlWithParams,
            ]);

            // 5. Log de diagnóstico
            Log::info(sprintf(
                "Sesión Stripe creada - ID: %s | Monto: %s COP | Orden: %s | URL: %s",
                $session->id,
                $total,
                $metadata['orden_id'],
                $session->url
            ));

            return [
                'url' => $session->url,
                'session_id' => $session->id
            ];

        } catch (ApiErrorException $e) {
            Log::error("Error al crear sesión Stripe: " . $e->getMessage());
            throw $e;
        }
    }
}