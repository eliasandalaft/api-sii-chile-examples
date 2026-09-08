#!/bin/bash
# Consulta una empresa chilena por RUT.
# Uso: API_KEY=tu_api_key ./consultar_rut.sh 93834000-5

set -euo pipefail

RUT="${1:?Uso: $0 <rut-con-dv>  (ej: 93834000-5)}"
API_KEY="${API_KEY:?Define la variable de entorno API_KEY con tu clave}"

curl -sS \
  -H "X-Api-Key: ${API_KEY}" \
  "https://api-sii-chile.webempresario.com/v1/${RUT}"
