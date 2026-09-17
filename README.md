# API SII Chile

API REST para consultar datos de empresas chilenas por RUT: razón social, giro,
actividades económicas, domicilios (con coordenadas GPS incluidas) y vigencia —
directo del padrón público del SII, sin necesidad de credenciales tributarias.

**Sitio:** https://api-sii-chile.webempresario.com
**Demo pública:** https://api-sii-chile.webempresario.com/#demo
**Registro (25 consultas gratis):** https://api-sii-chile.webempresario.com/#precios

## Por qué esta API

- **Coordenadas GPS incluidas.** La mayoría de las APIs de RUT solo devuelven la
  dirección en texto — hay que geocodificarla tú, con otro servicio. Acá la
  latitud/longitud de cada domicilio viene en la misma respuesta.
- **Todas las sucursales, no solo el domicilio principal.** Útil para logística,
  KYC y análisis territorial.
- **Vigencia real, no solo formato de RUT.** Indica si la empresa sigue activa o
  hizo término de giro, no solo si el dígito verificador es matemáticamente
  correcto.

## Autenticación

Todas las consultas requieren tu API Key en el header `X-Api-Key`. La obtienes al
registrarte en el sitio.

```
X-Api-Key: TU_API_KEY
```

## Quickstart

```bash
curl -H "X-Api-Key: TU_API_KEY" \
  https://api-sii-chile.webempresario.com/v1/93834000-5
```

Ejemplos completos en varios lenguajes en [`examples/`](./examples):

- [cURL](./examples/curl)
- [JavaScript / Node.js](./examples/javascript)
- [Python](./examples/python)
- [PHP](./examples/php)

## Cliente PHP (Composer)

Este repo también es un paquete de Composer instalable:

```bash
composer require webempresario/api-sii-chile
```

```php
use Webempresario\ApiSiiChile\Client;
use Webempresario\ApiSiiChile\ApiException;

$client = new Client('TU_API_KEY');

try {
    $empresa = $client->consultarRut('93834000-5');
    echo $empresa['RAZON_SOCIAL'];

    foreach ($empresa['domicilios'] as $domicilio) {
        echo $domicilio['GEO']['LAT'] . ', ' . $domicilio['GEO']['LON'];
    }
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## Endpoint

### `GET /v1/{rut}`

Consulta una empresa por su RUT (con dígito verificador, separado por guión).

**Parámetros de ruta**

| Parámetro | Tipo   | Descripción                          |
|-----------|--------|---------------------------------------|
| `rut`     | string | RUT con DV, ej. `93834000-5`         |

**Headers**

| Header      | Requerido | Descripción          |
|-------------|-----------|-----------------------|
| `X-Api-Key` | Sí        | Tu API Key personal   |

**Respuesta (200 OK)**

```json
{
  "success": true,
  "data": {
    "RUT": 93834000,
    "DV": "5",
    "TIPO": "PERSONA JURIDICA COMERCIAL",
    "RAZON_SOCIAL": "FRIGORIFICO DE OSORNO S A",
    "FECHA_INICIO_VIG": "01-01-1993",
    "FECHA_TG_VIG": "",
    "actividades": [
      {
        "CODIGO_ACTIVIDAD": 101020,
        "DESC_ACTIVIDAD": "ELABORACION Y CONSERVACION DE CARNE Y PRODUCTOS CARNICOS",
        "FECHA_ACTECO": "02-12-2003",
        "AFECTA_IVA": "S"
      }
    ],
    "domicilios": [
      {
        "CALLE": "AVDA LUIS PASTEUR",
        "NUMERO": "5753",
        "COMUNA": "VITACURA",
        "REGION": "XIII REGION METROPOLITANA",
        "TIPO_DIRECCION": "SUCURSAL",
        "GEO": {
          "LAT": -33.389425839393944,
          "LON": -70.5782439969697,
          "PRECISION": "exacta"
        }
      }
    ]
  }
}
```

`FECHA_TG_VIG` vacío significa que la empresa está vigente (sin término de giro).
Si trae una fecha, la empresa cesó actividades en esa fecha.

**Errores**

| Código | Significado                              |
|--------|--------------------------------------------|
| `400`  | Formato de RUT inválido                    |
| `401`  | API Key faltante o inválida                |
| `404`  | RUT no encontrado en el padrón del SII     |
| `429`  | Límite de consultas por minuto excedido    |

## Postman

Colección lista para importar con los 5 casos de respuesta (200, 400, 401, 404, 429):
[`examples/postman/API_SII_Chile.postman_collection.json`](./examples/postman/API_SII_Chile.postman_collection.json)

En Postman: **Import** → pega la URL raw de ese archivo en GitHub (o arrástralo). Luego configura la variable de colección `apiKey` con tu API Key.

## Guías

- [Cómo buscar una empresa por su RUT en el SII](https://api-sii-chile.webempresario.com/guias/buscar-empresa-por-rut/)
- [Cómo validar el RUT de una empresa con una API](https://api-sii-chile.webempresario.com/guias/validar-rut-empresa-api/)
- [Cómo saber si una empresa está vigente en el SII](https://api-sii-chile.webempresario.com/guias/empresa-vigente-sii/)

## Precios

Prepago de consultas, sin vencimiento. Detalle actualizado en
https://api-sii-chile.webempresario.com/#precios.

## Soporte

contacto@webempresario.com
