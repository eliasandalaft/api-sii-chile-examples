<?php

namespace Webempresario\ApiSiiChile;

/**
 * Se lanza cuando la API SII Chile responde con un error (RUT no encontrado, API Key
 * inválida, límite de consultas excedido, etc.) o cuando falla la conexión.
 */
class ApiException extends \RuntimeException
{
}
