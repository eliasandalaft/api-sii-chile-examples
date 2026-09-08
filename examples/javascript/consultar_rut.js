// Consulta una empresa chilena por RUT.
// Funciona en Node.js 18+ (fetch nativo) y en el navegador.
//
// Uso:
//   API_KEY=tu_api_key node consultar_rut.js 93834000-5

async function consultarEmpresaPorRut(rut, apiKey) {
  const res = await fetch(`https://api-sii-chile.webempresario.com/v1/${rut}`, {
    headers: { 'X-Api-Key': apiKey },
  });

  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(`API error ${res.status}: ${err.message || res.statusText}`);
  }

  return res.json();
}

// --- Ejemplo de uso (Node.js) ---
if (typeof require !== 'undefined' && require.main === module) {
  const rut = process.argv[2];
  const apiKey = process.env.API_KEY;

  if (!rut || !apiKey) {
    console.error('Uso: API_KEY=tu_api_key node consultar_rut.js <rut-con-dv>');
    process.exit(1);
  }

  consultarEmpresaPorRut(rut, apiKey)
    .then((data) => {
      console.log(JSON.stringify(data, null, 2));

      // Ejemplo: acceder a las coordenadas del primer domicilio
      const primerDomicilio = data.data.domicilios[0];
      if (primerDomicilio?.GEO?.LAT) {
        console.log(`\nCoordenadas del domicilio principal: ${primerDomicilio.GEO.LAT}, ${primerDomicilio.GEO.LON}`);
      }
    })
    .catch((err) => {
      console.error(err.message);
      process.exit(1);
    });
}

module.exports = { consultarEmpresaPorRut };
