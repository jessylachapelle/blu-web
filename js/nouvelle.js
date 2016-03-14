function modifier_nouvelle(id) {
  document.getElementById("actionTitre").innerHTML = "Modifier une nouvelle";
  document.getElementById("actionTexte").innerHTML = "Vous pouvez modifier une nouvelle existante à l'aide de ce formulaire.";
  fullId = "nouvelle_" + id;
  
  document.getElementById("id_nouvelle").value = id;
  document.getElementById("titre").value = document.getElementById(fullId).children[0].innerHTML;
  document.getElementById("message").value = document.getElementById(fullId).children[1].innerHTML;
  document.getElementById("debut").value =  document.getElementById(fullId).children[2].innerHTML;
  document.getElementById("fin").value = document.getElementById(fullId).children[3].innerHTML;
  document.getElementById("Annuler").style = "display: inline";
}

function resetText() {
  document.getElementById("actionTitre").innerHTML = "Ajouter une nouvelle";
  document.getElementById("actionTexte").innerHTML = "Vous pouvez ajouter une nouvelle au site à l'aide de ce formulaire.";
}
