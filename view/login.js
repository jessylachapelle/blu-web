function getParams() {
  return location.search.replace('\?', '').split('&').reduce(function (params, param) {
    if (param) {
      const keyValue = param.split('=');
      params[keyValue[0]] = keyValue[1];
    }

    return params;
  }, {});
}

const params = getParams();

if (params.error) {
  const errorMessage = {
    401: 'Erreur de connexion, veuillez vérifier votre adresse courriel et votre numéro de dossier',
  };

  document.getElementById('error').innerText = errorMessage[params.error] || '';
}
