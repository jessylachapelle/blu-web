"use strict";
var editionSelectors = document.getElementsByClassName("selectEdition");
var radioButtons = document.forms["statInputs"].elements["type"];
var chart;
var chartData;
var articles;

blockSelector();
eventHandlers();

// Défini les event listeners
function eventHandlers() {
  document.getElementById("selectStats").addEventListener("change", function(event) {
    document.getElementById("choixStat").style.display = "none";
    document.getElementById("transactionDate").style.display = "none";
    document.getElementById("transactionIntervale").style.display = "none";
    document.getElementById("stats").innerHTML = "";
    chartData = null;
    chart = null;

    switch (event.target.options[event.target.selectedIndex].value) {
      case "1":
        document.getElementById("choixStat").style.display = "block";
        document.getElementById("transactionDate").style.display = "block";
        break;
      case "2":
        document.getElementById("choixStat").style.display = "block";
        document.getElementById("transactionIntervale").style.display = "block";
        intervalChart(editionSelectors.debut.selectedIndex);
        break;
      case "3":
        argentARemettre();
        break;
      case "4":
        compteBLU();
        break;
      case "5":
        livresValidesNonVendus();
        break;
    }
  });

  for(var i = 0; i < radioButtons.length; i++) {
    radioButtons[i].addEventListener("change", function(event) {
      if(chartData) {
        document.getElementById("stats").innerHTML = "";
        chart = null;

        if(document.getElementById("selectStats").options[document.getElementById("selectStats").selectedIndex].value == 1) {
          barChart(chartData);
        } else {
          intervalChart(chartData);
        }
      }
    });
  }

  document.getElementById('jour').addEventListener("change", function(event) {
    document.getElementById("stats").innerHTML = "";
    barChart(event.target.value);
  });

  editionSelectors.debut.addEventListener("change", function(event) {
    document.getElementById("stats").innerHTML = "";
    var selector = event.target;

    if(selector.selectedIndex < editionSelectors.fin.selectedIndex) {
      var nbEditions = editionSelectors.fin.options.length;

      for(var i = 0; i < nbEditions; i++)
        editionSelectors.fin.options[i].removeAttribute("selected");
      editionSelectors.fin.options[index].setAttribute("selected", "true");
    }

    blockSelector();
    intervalChart(editionSelectors.debut.selectedIndex);
  });

  editionSelectors.fin.addEventListener("change", function() {
    document.getElementById("stats").innerHTML = "";
    intervalChart(editionSelectors.debut.selectedIndex);
  });
}

// Fonction AJAX
function getXMLHttpRequest() {
	var xmlhttp = null;

	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xmlhttp = new XMLHttpRequest();
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	return xmlhttp;
}

function getData(functionName, data, callback) {
  var xmlhttp = new getXMLHttpRequest();
  var formData = new FormData();

  formData.append('f', functionName);
  formData.append('data', data);

  xmlhttp.onreadystatechange = function(res) {
    if (xmlhttp.readyState==4) {
      callback(JSON.parse(xmlhttp.responseText));
    }
  };

  xmlhttp.open("POST", "res/query_stats.php", true);
  xmlhttp.send(formData);
}

// Bloque le sélecteur de fin à celui de début
function blockSelector() {
  var nbEditions = editionSelectors.fin.options.length;

  for(var i = 0; i < nbEditions; i++) {
    editionSelectors.fin.options[i].removeAttribute("disabled");
  }

  var index = editionSelectors.debut.selectedIndex;
  var nbEditions = editionSelectors.fin.options.length;

  for(var i = index + 1; i < nbEditions; i++) {
    editionSelectors.fin.options[i].setAttribute("disabled", "true");
  }
}


function intervalChart(data) {
  if(typeof data === 'object') {
    Object.keys(data).forEach(function(key) {
      if(!chart) {
        var ctx = createCanvas("editionChart").getContext("2d");
        chart = new Chart(ctx).Line(getChartData(data[key], document.querySelector('input[name="type"]:checked').value, key));
      } else {
        chart.addData(getChartData(data[key], document.querySelector('input[name="type"]:checked').value), key);
        chart.update();
      }
    });
  } else {
    getData('transactionIntervale', editionSelectors.debut.options[data].value, function(res) {
      var label = editionSelectors.debut.options[data].value;

      if (!chartData) {
        chartData = {};
      } chartData[label] = res;

      if (data === editionSelectors.debut.selectedIndex) {
        var ctx = createCanvas("editionChart").getContext("2d");
        chart = new Chart(ctx).Line(getChartData(res, document.querySelector('input[name="type"]:checked').value, editionSelectors.debut.options[data].value));
      } else {
        chart.addData(getChartData(res, document.querySelector('input[name="type"]:checked').value), editionSelectors.debut.options[data].value);
        chart.update();
      }

      return data === editionSelectors.fin.selectedIndex || intervalChart(--data);
    });
  }
}

function barChart(data) {
  if(typeof data === 'object') {
    var ctx = createCanvas("dateChart").getContext("2d");
    new Chart(ctx).Bar((getChartData(data, document.querySelector('input[name="type"]:checked').value, "label")));
  } else {
    getData('transactionDate', data, function(res) {
      chartData = res;
      var ctx = createCanvas("dateChart").getContext("2d");
      new Chart(ctx).Bar((getChartData(res, document.querySelector('input[name="type"]:checked').value, "label")));
    });
  }
}

function getChartData(jsonData, dataType, label) {
  var misEnVenteData = parseInt(jsonData["misEnVente"][dataType]) || 0;
  var venteData = parseInt(jsonData["vente"][dataType]) || 0;
  var venteParentEtudiantData = parseInt(jsonData["venteParentEtudiant"][dataType]) || 0;
  var argentRemisData = parseInt(jsonData["argentRemis"][dataType]) || 0;

  if(label) {
    var datasets = [
      {
        label: "Mis en vente",
        fillColor: "rgba(160, 179, 137, 0.2)",
        strokeColor: "rgba(160, 179, 137, 1)",
        pointColor: "rgba(160, 179, 137, 1)",
        highlightFill: "rgba(160, 179, 137, 1)",
        highlightStroke: "rgba(160, 179, 137, 1)",
        pointHighlightFill: "#000",
        pointHighlightStroke: "rgba(160, 179, 137, 1)",
        data: [misEnVenteData]
      },
      {
        label: "Vendu",
        fillColor: "rgba(187, 119, 57, 0.2)",
        strokeColor: "rgba(187, 119, 57, 1)",
        pointColor: "rgba(187, 119, 57, 1)",
        highlightFill: "rgba(187, 119, 57, 1)",
        highlightStroke: "rgba(187, 119, 57, 1)",
        pointHighlightFill: "#000",
        pointHighlightStroke: "rgba(187, 119, 57, 1)",
        data: [venteData]
      },
      {
        label: "Vendu à 50%",
        fillColor: "rgba(191, 54, 12, 0.2)",
        strokeColor: "rgb(191, 54, 12)",
        pointColor: "rgb(191, 54, 12)",
        highlightFill: "rgb(191, 54, 12)",
        ighlightStroke: "rgb(191, 54, 12)",
        pointHighlightFill: "#000",
        pointHighlightStroke: "rgb(191, 54, 12)",
        data: [venteParentEtudiantData]
      },
      {
        label: "Argent remis",
        fillColor: "rgba(51, 102, 153, 0.2)",
        strokeColor: "rgb(51, 102, 153)",
        pointColor: "rgb(51, 102, 153)",
        highlightFill: "rgb(51, 102, 153)",
        highlightStroke: "rgb(51, 102, 153)",
        pointHighlightFill: "#000",
        pointHighlightStroke: "rgb(51, 102, 153)",
        data: [argentRemisData]
      }
    ];

    return {
      labels: [label],
      datasets: datasets
    };
  }

  return [
    misEnVenteData,
    venteData,
    venteParentEtudiantData,
    argentRemisData
  ];
}

function createCanvas(id) {
  var canvas = document.createElement("canvas");

  canvas.setAttribute("id", id);
  canvas.setAttribute("width", "800");
  canvas.setAttribute("height", "600");

  document.getElementById("stats").appendChild(canvas);
  return canvas;
}

function argentARemettre() {
  getData('argentARemettre', true, function(res) {
    var total = res.total;
    delete res.total;
    createTable(res, total);
  });
}

function createTable(members, total) {
  var title = document.createElement('h2');
  title.appendChild(document.createTextNode("Argent total à remettre " + total + "$"));

  var table = document.createElement('table');
  table.setAttribute('class', 'tablesorter');

  var thead = document.createElement('thead');
  var headRow = document.createElement('tr');
  var tbody = document.createElement("tbody");

  var th = document.createElement('th');
  th.appendChild(document.createTextNode("No étudiant"));
  headRow.appendChild(th);

  th = document.createElement('th');
  th.appendChild(document.createTextNode("Nom"));
  headRow.appendChild(th);

  th = document.createElement('th');
  th.appendChild(document.createTextNode("Prénom"));
  headRow.appendChild(th);

  th = document.createElement('th');
  th.appendChild(document.createTextNode("Montant dû"));
  headRow.appendChild(th);

  thead.appendChild(headRow);
  table.appendChild(thead);

  Object.keys(members).forEach(function(key) {
    var bodyRow = document.createElement('tr');

    var td = document.createElement('td');
    td.appendChild(document.createTextNode(members[key]['no']));
    bodyRow.appendChild(td);

    td = document.createElement('td');
    td.appendChild(document.createTextNode(members[key]['nom']));
    bodyRow.appendChild(td);

    td = document.createElement('td');
    td.appendChild(document.createTextNode(members[key]['prenom']));
    bodyRow.appendChild(td);

    td = document.createElement('td');
    td.appendChild(document.createTextNode(members[key]['montant'] + "$"));
    bodyRow.appendChild(td);

    tbody.appendChild(bodyRow);
  });

  table.appendChild(tbody);
  document.getElementById('stats').appendChild(title);
  document.getElementById('stats').appendChild(table);

  sortTables();
}

function compteBLU() {
  getData('blu', true, function(res) {
    var strPassif = "La BLU possède " + res.passif.quantite + " livres en vente pour un montant de " + res.passif.montant + "$";
    var strActif = "La BLU a récupéré un montant de " + res.actif.montant + "$ provenant de comptes désactivés"
    var passif = document.createElement('p');
    var actif = document.createElement('p');

    passif.appendChild(document.createTextNode(strPassif));
    actif.appendChild(document.createTextNode(strActif));

    document.getElementById("stats").appendChild(passif);
    document.getElementById("stats").appendChild(actif);
  });
}

function livresValidesNonVendus() {
  getData('livresValidesNonVendus', null, function(res) {
    console.log(res);

    articles = res;
    var table = document.createElement('table');
    table.setAttribute('class', 'tablesorter');

    var thead = document.createElement('thead');
    var tbody = document.createElement('tbody');
    var rowHead = document.createElement('tr');
    var titleHead = document.createElement('th');
    var categoryHead = document.createElement('th');

    titleHead.appendChild(document.createTextNode('Titre'));
    categoryHead.appendChild(document.createTextNode('Catégorie'));
    rowHead.appendChild(titleHead);
    rowHead.appendChild(categoryHead);
    thead.appendChild(rowHead);
    table.appendChild(thead);

    Object.keys(articles).forEach(function(id) {
      var article = articles[id];
      var tr = document.createElement('tr');
      var title = document.createElement('td');
      var category = document.createElement('td');

      tr.setAttribute("data-article", article.id);
      tr.setAttribute("onclick", "openArticle(this)");

      title.appendChild(document.createTextNode(article.title));
      category.appendChild(document.createTextNode(article.category));

      tr.appendChild(title);
      tr.appendChild(category);
      tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    document.getElementById('stats').appendChild(table);

    sortTables();
  });
}
