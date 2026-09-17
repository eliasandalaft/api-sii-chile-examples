<?php

namespace Webempresario\ApiSiiChile;

/**
 * Cliente para la API SII Chile (https://api-sii-chile.webempresario.com).
 *
 * Ejemplo:
 *   $client = new Client('TU_API_KEY');
 *   $empresa = $client->consultarRut('93834000-5');
 *   echo $empresa['RAZON_SOCIAL'];
 */
class Client
{
    private const BASE_URL = 'https://api-sii-chile.webempresario.com/v1';

    private string $apiKey;
    private int $timeout;

    public function __construct(string $apiKey, int $timeout = 10)
    {
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    /**
     * Consulta una empresa chilena por su RUT (con dígito verificador, ej: "93834000-5").
     *
     * @return array Datos de la empresa: RAZON_SOCIAL, TIPO, actividades[], domicilios[]
     *               (cada domicilio incluye GEO.LAT / GEO.LON / GEO.PRECISION).
     * @throws ApiException Si el RUT no existe, la API Key es inválida, o falla la conexión.
     */
    public function consultarRut(string $rut): array
    {
        $ch = curl_init(self::BASE_URL . '/' . rawurlencode($rut));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["X-Api-Key: {$this->apiKey}"],
            CURLOPT_TIMEOUT        => $this->timeout,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ApiException("Error de conexión: {$error}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new ApiException('Respuesta inválida de la API (no es JSON válido).');
        }

        if ($httpCode !== 200) {
            $mensaje = $data['message'] ?? "HTTP {$httpCode}";
            throw new ApiException($mensaje, $httpCode);
        }

        return $data['data'];
    }
}
