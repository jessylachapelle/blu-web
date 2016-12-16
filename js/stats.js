'use strict';
let editionSelectors = document.getElementsByClassName('selectEdition');
let radioButtons = document.forms['statInputs'].elements['type'];
let chart;
let chartData;
let items;

blockSelector();
eventHandlers();

// Défini les event listeners
function eventHandlers() {
  document.getElementById('selectStats').addEventListener('change', (event) => {
    document.getElementById('choixStat').style.display = 'none';
    document.getElementById('transactionDate').style.display = 'none';
    document.getElementById('transactionIntervale').style.display = 'none';
    document.getElementById('stats').innerHTML = '';
    chartData = null;
    chart = null;

    switch (+event.target.options[event.target.selectedIndex].value) {
      case 1:
        document.getElementById('choixStat').style.display = 'block';
        document.getElementById('transactionDate').style.display = 'block';
        break;
      case 2:
        document.getElementById('choixStat').style.display = 'block';
        document.getElementById('transactionIntervale').style.display = 'block';
        intervalChart(editionSelectors.debut.selectedIndex);
        break;
      case 3:
        argentARemettre();
        break;
      case 4:
        compteBLU();
        break;
      case 5:
        livresValidesNonVendus();
        break;
    }
  });

  for (let i = 0; i < radioButtons.length; i++) {
    radioButtons[i].addEventListener('change', (event) => {
      if (chartData) {
        document.getElementById('stats').innerHTML = '';
        chart = null;

        const selectStats = document.getElementById('selectStats');
        if (selectStats.options[selectStats.selectedIndex].value == 1) {
          barChart(chartData);
        } else {
          intervalChart(chartData);
        }
      }
    });
  }

  document.getElementById('jour').addEventListener('change', (event) => {
    document.getElementById('stats').innerHTML = '';
    barChart(event.target.value);
  });

  editionSelectors.debut.addEventListener('change', (event) => {
    document.getElementById('stats').innerHTML = '';
    const selector = event.target;

    if (selector.selectedIndex < editionSelectors.fin.selectedIndex) {
      const numEditions = editionSelectors.fin.options.length;

      for (let i = 0; i < numEditions; i++) {
        editionSelectors.fin.options[i].removeAttribute('selected');
      }

      editionSelectors.fin.options[index].setAttribute('selected', 'true');
    }

    blockSelector();
    intervalChart(editionSelectors.debut.selectedIndex);
  });

  editionSelectors.fin.addEventListener('change', () => {
    document.getElementById('stats').innerHTML = '';
    intervalChart(editionSelectors.debut.selectedIndex);
  });
}

function getXMLHttpRequest() {
	if (window.ActiveXObject) {
		try {
			return new ActiveXObject('Msxml2.XMLHTTP');
		} catch (e) {
			return new ActiveXObject('Microsoft.XMLHTTP');
		}
	}

	return window.XMLHttpRequest ?  new XMLHttpRequest() : null;
}

function getData(functionName, data, callback) {
  const xmlhttp = getXMLHttpRequest();
  const formData = new FormData();

  formData.append('f', functionName);
  formData.append('data', data);

  xmlhttp.onreadystatechange = () => {
    if (xmlhttp.readyState == 4) {
      const res = JSON.parse(xmlhttp.responseText);
      console.log(Object.keys(res).length); // 2095
      callback(res);
    }
  };

  xmlhttp.open('POST', 'res/query_stats.php', true);
  xmlhttp.send(formData);
}

// Bloque le sélecteur de fin à celui de début
function blockSelector() {
  const numEditions = editionSelectors.fin.options.length;

  for (let i = 0; i < numEditions; i++) {
    editionSelectors.fin.options[i].removeAttribute('disabled');
  }

  const index = editionSelectors.debut.selectedIndex;

  for (let i = index + 1; i < numEditions; i++) {
    editionSelectors.fin.options[i].setAttribute('disabled', 'true');
  }
}


function intervalChart(data) {
  const ctx = createCanvas('editionChart').getContext('2d');
  const input = document.querySelector('input[name="type"]:checked').value;

  if (typeof data === 'object') {
    Object.keys(data).forEach((key) => {
      const intervalChartData = getChartData(data[key], input, key);

      if (!chart) {
        chart = new Chart(ctx).Line(intervalChartData);
      } else {
        chart.addData(intervalChartData);
        chart.update();
      }
    });
  } else {
    getData('transactionIntervale', editionSelectors.debut.options[data].value, (res) => {
      const label = editionSelectors.debut.options[data].value;

      if (!chartData) {
        chartData = {};
      }

      chartData[label] = res;
      const es = editionSelectors.debut.options[data].value;

      if (data === editionSelectors.debut.selectedIndex) {
        chart = new Chart(ctx).Line(getChartData(res, input, es));
      } else {
        chart.addData(getChartData(res, input), es);
        chart.update();
      }

      return data === editionSelectors.fin.selectedIndex || intervalChart(--data);
    });
  }
}

function barChart(data) {
  const ctx = createCanvas('dateChart').getContext('2d');
  const input = document.querySelector('input[name="type"]:checked').value
  const label = 'label';

  if (typeof data === 'object') {
    new Chart(ctx).Bar(getChartData(data, input, label));
  } else {
    getData('transactionDate', data, (res) => {
      chartData = res;
      const ctx = createCanvas('dateChart').getContext('2d');
      new Chart(ctx).Bar(getChartData(res, input, label));
    });
  }
}

function getChartData(jsonData, dataType, label) {
  const misEnVenteData = +jsonData.misEnVente[dataType] || 0;
  const venteData = +jsonData.vente[dataType] || 0;
  const venteParentEtudiantData = +jsonData.venteParentEtudiant[dataType] || 0;
  const argentRemisData = +jsonData.argentRemis[dataType] || 0;

  if (label) {
    const datasets = [
      {
        label: 'Mis en vente',
        fillColor: 'rgba(160, 179, 137, 0.2)',
        strokeColor: 'rgba(160, 179, 137, 1)',
        pointColor: 'rgba(160, 179, 137, 1)',
        highlightFill: 'rgba(160, 179, 137, 1)',
        highlightStroke: 'rgba(160, 179, 137, 1)',
        pointHighlightFill: '#000',
        pointHighlightStroke: 'rgba(160, 179, 137, 1)',
        data: [misEnVenteData]
      },
      {
        label: 'Vendu',
        fillColor: 'rgba(187, 119, 57, 0.2)',
        strokeColor: 'rgba(187, 119, 57, 1)',
        pointColor: 'rgba(187, 119, 57, 1)',
        highlightFill: 'rgba(187, 119, 57, 1)',
        highlightStroke: 'rgba(187, 119, 57, 1)',
        pointHighlightFill: '#000',
        pointHighlightStroke: 'rgba(187, 119, 57, 1)',
        data: [venteData]
      },
      {
        label: 'Vendu à 50%',
        fillColor: 'rgba(191, 54, 12, 0.2)',
        strokeColor: 'rgb(191, 54, 12)',
        pointColor: 'rgb(191, 54, 12)',
        highlightFill: 'rgb(191, 54, 12)',
        ighlightStroke: 'rgb(191, 54, 12)',
        pointHighlightFill: '#000',
        pointHighlightStroke: 'rgb(191, 54, 12)',
        data: [venteParentEtudiantData]
      },
      {
        label: 'Argent remis',
        fillColor: 'rgba(51, 102, 153, 0.2)',
        strokeColor: 'rgb(51, 102, 153)',
        pointColor: 'rgb(51, 102, 153)',
        highlightFill: 'rgb(51, 102, 153)',
        highlightStroke: 'rgb(51, 102, 153)',
        pointHighlightFill: '#000',
        pointHighlightStroke: 'rgb(51, 102, 153)',
        data: [argentRemisData]
      }
    ];

    return {
      labels: [label],
      datasets,
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
  const canvas = document.createElement('canvas');

  canvas.setAttribute('id', id);
  canvas.setAttribute('width', '800');
  canvas.setAttribute('height', '600');

  document.getElementById('stats').appendChild(canvas);
  return canvas;
}

function argentARemettre() {
  getData('argentARemettre', true, (res) => {
    const total = res.total;
    delete res.total;
    createTable(res, total);
  });
}

function createTable(members, total) {
  const stats = document.getElementById('stats');
  const title = document.createElement('h2');
  const table = document.createElement('table');
  const thead = document.createElement('thead');
  const headRow = document.createElement('tr');
  const tbody = document.createElement('tbody');
  const columns = [
    { key: 'no', title: 'No étudiant'},
    { key: 'nom', title: 'Nom' },
    { key: 'prenom', title: 'Prénom' },
    { key: 'montant', title: 'Montant dû' }
  ];

  title.appendChild(document.createTextNode(`Argent total à remettre ${total}$`));
  table.setAttribute('class', 'tablesorter');

  columns.forEach((column) => {
    const th = document.createElement('th');
    th.appendChild(document.createTextNode(column.title));
    headRow.appendChild(th);
  });

  thead.appendChild(headRow);
  table.appendChild(thead);

  Object.keys(members).forEach((key) => {
    const tr = document.createElement('tr');

    columns.forEach((column) => {
      const td = document.createElement('td');
      td.appendChild(document.createTextNode(members[key][column.key]));
      tr.appendChild(td);
    });

    tbody.appendChild(tr);
  });

  table.appendChild(tbody);
  stats.appendChild(title);
  stats.appendChild(table);

  sortTables();
}

function compteBLU() {
  getData('blu', true, (res) => {
    const stats = document.getElementById('stats');
    const passif = document.createElement('p');
    const actif = document.createElement('p');
    const strPassif = `La BLU possède ${res.passif.quantite} livres en vente pour un montant de ${res.passif.montant}$`;
    const strActif = `La BLU a récupéré un montant de ${res.actif.montant}$ provenant de comptes désactivés`;

    passif.appendChild(document.createTextNode(strPassif));
    actif.appendChild(document.createTextNode(strActif));
    stats.appendChild(passif);
    stats.appendChild(actif);
  });
}

function livresValidesNonVendus() {
  getData('livresValidesNonVendus', null, (res) => {
    items = res;
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');
    const rowHead = document.createElement('tr');
    const columns = [
      { key: 'title', title: 'Titre' },
      { key: 'category', title: 'Catégorie' }
    ];

    table.setAttribute('class', 'tablesorter');

    columns.forEach((column) => {
      const th = document.createElement('th');
      th.appendChild(document.createTextNode(column.title));
      rowHead.appendChild(th);
    });

    thead.appendChild(rowHead);
    table.appendChild(thead);

    Object.keys(items).forEach((id) => {
      const item = items[id];
      const tr = document.createElement('tr');

      columns.forEach((column) => {
        const td = document.createElement('td');
        td.appendChild(document.createTextNode(item[column.key]));
        tr.appendChild(td);
      });

      tr.setAttribute('data-article', item.id);
      tr.setAttribute('onclick', 'openArticle(this)');
      tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    document.getElementById('stats').appendChild(table);

    sortTables();
  });
}
