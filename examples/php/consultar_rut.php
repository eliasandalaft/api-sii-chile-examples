<?php
/**
 * Consulta una empresa chilena por RUT.
 *
 * Uso: API_KEY=tu_api_key php consultar_rut.php 93834000-5
 */

function consultarEmpresaPorRut(string $rut, string $apiKey): array
{
    $ch = curl_init("https://api-sii-chile.webempresario.com/v1/{$rut}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["X-Api-Key: {$apiKey}"],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    if ($httpCode !== 200) {
        $mensaje = $data['message'] ?? "HTTP {$httpCode}";
        throw new RuntimeException("Error de la API: {$mensaje}");
    }

    return $data;
}

if (php_sapi_name() === 'cli' && isset($argv)) {
    $rut = $argv[1] ?? null;
    $apiKey = getenv('API_KEY') ?: null;

    if (!$rut || !$apiKey) {
        fwrite(STDERR, "Uso: API_KEY=tu_api_key php consultar_rut.php <rut-con-dv>\n");
        exit(1);
    }

    $empresa = consultarEmpresaPorRut($rut, $apiKey);
    echo json_encode($empresa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

    // Ejemplo: acceder a las coordenadas del primer domicilio
    $primerDomicilio = $empresa['data']['domicilios'][0] ?? null;
    if ($primerDomicilio && !empty($primerDomicilio['GEO']['LAT'])) {
        $geo = $primerDomicilio['GEO'];
        echo "\nCoordenadas del domicilio principal: {$geo['LAT']}, {$geo['LON']}\n";
    }
}
