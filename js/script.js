// Fonction AJAX
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

const HTTP = {
	call: (method, url, data, callback) => {
		const xmlhttp = new getXMLHttpRequest();

		xmlhttp.onreadystatechange = () => {
			if (xmlhttp.readyState == 4) {
				callback(xmlhttp.responseText);
			}
		}

		xmlhttp.open(method, url, true);
		xmlhttp.send(data);
	}
}

function getParameterByName(name, url) {
	if (!url || url == '') {
		url = window.location.href;
	}

  const regex = new RegExp(`[?&]${name.replace(/[\[\]]/g, '\\$&')}(=([^&#]*)|&|#|$)`);
  const results = regex.exec(url);

  if (!results) {
		return null;
	}

  if (!results[2]) {
		return '';
	}

	return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function openArticle(e) {
  document.location.href = `article.php?article=${e.getAttribute('data-article')}`;
}

function miseAJourCompte() {
  document.getElementById('memberNo').value = memberNo;
  document.getElementById('address').value = address;
  document.getElementById('codepostal').value = codePostal;
  document.getElementById('ville').value = ville;
  document.getElementById('courriel').value = courriel;
  document.getElementById('idtel1').value = idTel1;
  document.getElementById('telephone1').value = tel1;
  document.getElementById('note1').value = note1;
  document.getElementById('idtel2').value = idTel2;
  document.getElementById('telephone2').value = tel2;
  document.getElementById('note2').value = note2;

  overlay.style.display = 'block';
}

function closeOverlay() {
  document.getElementById('overlay').style.display = 'none';;
}

function openSignal() {
  document.getElementById('signalement').style.display = 'block';
}

function closeSignal() {
  document.getElementById('signalement').style.display = 'none';
}

function createTooltip(event) {
  tooltip = document.createElement('div');
  const row = event.target.parentNode;
  const p = document.createElement('p');
  const tPosX = row.getBoundingClientRect().left + row.getBoundingClientRect().width + 20;
  const tPosY = row.getBoundingClientRect().top;
	const text = 'Cet article est désuet et ne sera plus vendu à la BLU. Si vous désirez le récupérer, veuillez contacter la BLU. Le cas échéant, il sera envoyé dans un programme de récupération de livres.';

  p.appendChild(document.createTextNode(text));
  tooltip.appendChild(p);
  tooltip.setAttribute('id', 'tooltip');
  tooltip.setAttribute('style', `top: ${tPosY}px; left: ${tPosX}px;`);

  document.body.appendChild(tooltip);
}

function deleteTooltip(event) {
  if (tooltip != null) {
		document.body.removeChild(document.getElementById('tooltip'));
    tooltip = null;
  }
}

function slideoutMenu() {
  const menuButton = document.getElementById('menu-button');
  const panel = document.getElementsByTagName('main')[0];
  const header = document.getElementsByTagName('header')[0];

  const slideout = new Slideout({
    panel,
    menu: document.getElementById('menu'),
    padding: 1,
    tolerance: 70
  });

  menuButton.addEventListener('click', () => {
    slideout.toggle();
  });

  header.addEventListener('click', () => {
    slideout.close();
  }, true);

  panel.addEventListener('click', () => {
    slideout.close();
  });
}

function search(event) {
	event.preventDefault();

	const data = new FormData();
	data.append('search-data', event.target.search.value);
	articles = {
		tout: {},
		titre: {},
		auteur: {},
		editeur: {}
	};

	HTTP.call('POST', 'res/search_query.php', data, (res) => {
		articles.tout = JSON.parse(res);

		Object.keys(articles.tout).forEach((id) => {
			const article = articles.tout[id];

			if (article.name.indexOf(event.target.search.value) > -1) {
				articles.titre[id] = article;
			}

			if (article.author.indexOf(event.target.search.value) > -1) {
				articles.auteur[id] = article;
			}

			if (article.editor.indexOf(event.target.search.value) > -1) {
				articles.editeur[id] = article;
			}
		});

		displayResults(event.target.filtre.value);
	});
}

function displayResults(filtre) {
	document.getElementById('resultat').innerHTML = "";

	const table = document.createElement('table');
	table.setAttribute('class', 'tablesorter');

	const thead = document.createElement('thead');
	const tbody = document.createElement('tbody');

	const headRow = document.createElement('tr');
	const headName = document.createElement('th');
	const headAuthor = document.createElement('th');
	const headEditor = document.createElement('th');

	headName.appendChild(document.createTextNode("Titre"));
	headAuthor.appendChild(document.createTextNode("Auteur(s)"));
	headEditor.appendChild(document.createTextNode("Éditeur"));

	headRow.appendChild(headName);
	headRow.appendChild(headAuthor);
	headRow.appendChild(headEditor);
	thead.appendChild(headRow);
	table.appendChild(thead);

	if (!articles || !articles[filtre]) {
		return;
	}

	Object.keys(articles[filtre]).forEach((id) => {
		const article = articles[filtre][id];

		const tr = document.createElement('tr');
		tr.setAttribute("data-article", article.id);
		tr.setAttribute("onclick", "openArticle(this)");

		const name = document.createElement('td');
		const author = document.createElement('td');
		const editor = document.createElement('td');

		name.appendChild(document.createTextNode(article.name));
		author.appendChild(document.createTextNode(article.author));
		editor.appendChild(document.createTextNode(article.editor));

		tr.appendChild(name);
		tr.appendChild(author);
		tr.appendChild(editor);

		tbody.appendChild(tr);
	});

	table.appendChild(tbody);
	document.getElementById('resultat').appendChild(table);
	sortTables();
}

function subscribe(e) {
	const state = e.getAttribute('data-state') === 'subscribed' ? 'unsubscribe' : 'subscribed'
	const data = new FormData();

	data.append('memberNo', memberNo);
	data.append('itemId', e.getAttribute('data-item'));
	data.append('f', state);

	HTTP.call('POST', 'res/article_subscription.php', data, (res) => {
		if (res) {
			e.setAttribute('data-state', state);
		} else {
			console.log(res);
		}
	});
}

function verifyCoordonates(event) {
	event.preventDefault();

  const inputNoAddress = event.target.address;
  const inputCodePostal = event.target.codepostal;
  const inputTel1 = event.target.telephone1;
  const inputTel2 = event.target.telephone2;
  const inputEmail = event.target.courriel;

  const regExCodePostal = /^([a-zA-Z]\d[a-zA-z]( )?\d[a-zA-Z]\d)$/i;
  const regExTelephone = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
  const regExEmail = /^.+@.+$/;

  if (!inputCodePostal.value.match(regExCodePostal)) {
    inputCodePostal.setAttribute('class', 'invalid');
    return;
  }

  if (inputCodePostal.class === 'invalid') {
    inputCodePostal.removeClass('invalid');
  }

  if (!inputTel1.value.match(regExTelephone)) {
    inputTel1.setAttribute('class', 'invalid');
    return;
  }

  if (inputTel1.class === 'invalid') {
    inputTel1.removeClass('invalid');
  }

  if (inputTel2.value && inputTel2.value !== '') {
    if (!inputTel2.value.match(regExTelephone)) {
      inputTel2.setAttribute('class', 'invalid');
      return;
    }

    if (inputTel1.class === 'invalid') {
      inputTel1.removeClass('invalid');
    }
  }

  if (!inputEmail.value.match(regExEmail)) {
    inputEmail.setAttribute('class', 'invalid');
    return;
  }

  if (inputEmail.class === 'invalid') {
    inputEmail.removeClass('invalid');
  }

	const data = new FormData();
  data.append('memberNo', event.target.memberNo.value);
  data.append('address', address.value);
  data.append('codepostal', inputCodePostal.value.replace(' ', ''));
  data.append('telephone1', inputTel1.value.replace(/\D/g, ''));
  data.append('idtel1', event.target.idtel1.value);
  data.append('telephone2', inputTel2.value.replace(/\D/g, ''));
  data.append('idtel2', event.target.idtel2.value);
  data.append('courriel', inputEmail.value);
  data.append('ville', event.target.ville.value);
  data.append('province', event.target.province.value);

	HTTP.call('POST', 'res/mise_a_jour.php', data, () => {
		document.location.href = 'index.php';
	});
}

function eventHandlers() {
	window.addEventListener('scroll', deleteTooltip);

	document.getElementById('search').addEventListener('search', (event) => {
		document.location.href = 'recherche.php?r=' + event.target.value;
	});

	const outdated = document.getElementsByClassName('outdated');
	for (let i = 0; i < outdated.length; i++) {
	  outdated[i].addEventListener('mouseover', createTooltip);
	  outdated[i].addEventListener('mouseout', deleteTooltip);
	}

	if (document.getElementById('search-form')) {
		document.getElementById('search-form').addEventListener('submit', search);

		const filtres = document.getElementsByName('filtre');
		for (let i = 0; i < filtres.length; i++) {
			filtres[i].addEventListener('change', (event) => {
				displayResults(event.target.value);
			});
		}
	}

	if(document.getElementById('coord-form')) {
		document.getElementById('coord-form').addEventListener('submit', verifyCoordonates);
	}
}

function sortTables() {
	$(document).ready(function() {
	  $('.tablesorter').tablesorter();
	});
}

/*===========
| EXECUTION |
===========*/
var articles;
var tooltip;
var r = getParameterByName('r');

if (window.Slideout) {
	slideoutMenu();
}

eventHandlers();

if (r && document.getElementById('recherche') && document.getElementById('btn-recherche')) {
	document.getElementById('recherche').value = r;
	document.getElementById('btn-recherche').click();
}

sortTables();
