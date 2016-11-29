<h1>Statistiques</h1>
<form name="statInputs">
  <select id="selectStats">
    <option disabled="" selected="">Faire un choix</option>
    <option value="1">Transactions par date</option>
    <option value="2">Transactions par intervale</option>
    <option value="3">Argent à remettre</option>
    <option value="4">Actifs et passifs de la BLU</option>
    <option value="5">Livres valides mais non-vendus</option>
  </select>
  <div id="choixStat">
    <input id="montant" type="radio" name="type" value="montant" checked="" />
    <label for="montant">Montant</label>
    <input id="quantite" type="radio" name="type" value="quantite" />
    <label for="quantite">Nombre de livres</label>
  </div>
  <div id="transactionDate">
    <label for="jour">Journée</label>
    <input id="jour" name="jour" type="date" />
  </div>
  <div id="transactionIntervale">
    <div>
      <?php selecteurEdition("debut", "Choisir l'édition de début", 5); ?>
    </div>
    <div>
      <?php selecteurEdition("fin", "Choisir l'édition de fin", 0); ?>
    </div>
  </div>
</form>
<section id="stats"></section>
