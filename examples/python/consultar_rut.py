"""Consulta una empresa chilena por RUT.

Uso:
    API_KEY=tu_api_key python consultar_rut.py 93834000-5

Requiere: pip install requests
"""
import os
import sys

import requests

API_BASE = "https://api-sii-chile.webempresario.com/v1"


def consultar_empresa_por_rut(rut: str, api_key: str) -> dict:
    resp = requests.get(f"{API_BASE}/{rut}", headers={"X-Api-Key": api_key}, timeout=10)
    resp.raise_for_status()
    return resp.json()


if __name__ == "__main__":
    if len(sys.argv) < 2:
        sys.exit("Uso: API_KEY=tu_api_key python consultar_rut.py <rut-con-dv>")

    rut_arg = sys.argv[1]
    api_key_env = os.environ.get("API_KEY")
    if not api_key_env:
        sys.exit("Define la variable de entorno API_KEY con tu clave")

    empresa = consultar_empresa_por_rut(rut_arg, api_key_env)
    print(empresa)

    # Ejemplo: acceder a las coordenadas del primer domicilio
    domicilios = empresa.get("data", {}).get("domicilios", [])
    if domicilios and domicilios[0].get("GEO", {}).get("LAT"):
        geo = domicilios[0]["GEO"]
        print(f"\nCoordenadas del domicilio principal: {geo['LAT']}, {geo['LON']}")
