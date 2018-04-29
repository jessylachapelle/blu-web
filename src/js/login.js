var params = getParams();

if (params.error) {
  var errorMessage = {
    401: 'Erreur de connexion, veuillez vérifier votre adresse courriel et votre numéro de dossier',
  };

  document.getElementById('error').innerText = errorMessage[params.error] || '';
}
