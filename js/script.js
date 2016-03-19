// Fonction AJAX
function getXMLHttpRequest() {
	var xmlhttp = null;

	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
			} catch(e) {
				xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
			}
		} else {
			xmlhttp = new XMLHttpRequest();
		}
	}
	return xmlhttp;
}

function getParameterByName(name, url) {
    if (!url) {
			url = window.location.href;
		}

		name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
  	var results = regex.exec(url);

    if (!results) {
			return null;
		}

    if (!results[2]) {
			return '';
		}

		return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function openArticle(e) {
  document.location.href = 'article.php?article=' + e.getAttribute('data-article');
}

function miseAJourCompte() {
  document.getElementById('memberNo').value = memberNo;
  document.getElementById('nocivic').value = noCivic;
  document.getElementById('rue').value = rue;
  document.getElementById('app').value = app;
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

function closeSignal(){
  document.getElementById('signalement').style.display = 'none';
}

function createTooltip(event) {
  tooltip = document.createElement('div');
  var row = event.target.parentNode;
  var p = document.createElement('p');
  var tPosX = row.getBoundingClientRect().left + row.getBoundingClientRect().width + 20;
  var tPosY = row.getBoundingClientRect().top;

  p.appendChild(document.createTextNode('Cet article est désuet et ne sera plus vendu à la BLU. Si vous désirez le récupérer, veuillez contacter la BLU. Le cas échéant, il sera envoyé dans un programme de récupération de livres.'));
  tooltip.appendChild(p);
  tooltip.setAttribute('id', 'tooltip');
  tooltip.setAttribute('style', 'top: ' + tPosY + 'px; left: ' + tPosX + 'px;');

  document.body.appendChild(tooltip);
}

function deleteTooltip(event) {
  if(tooltip != null) {
     document.body.removeChild(document.getElementById('tooltip'));
     tooltip = null;
  }
}

function slideoutMenu() {
  var menuButton = document.getElementById('menu-button');
  var main = document.getElementsByTagName('main')[0];
  var header = document.getElementsByTagName('header')[0];

  var slideout = new Slideout({
    'panel': main,
    'menu': document.getElementById('menu'),
    'padding': 1,
    'tolerance': 70
  });

  menuButton.addEventListener('click', function(event) {
    slideout.toggle();
  });

  header.addEventListener('click', function(event) {
    slideout.close();
  }, true);

  main.addEventListener('click', function(event) {
    slideout.close();
  });
}

function search(event) {
	event.preventDefault();

  var xmlhttp = new getXMLHttpRequest();
  var formData = new FormData();

  formData.append('search-data', event.target.search.value);

  xmlhttp.onreadystatechange = function(res) {
    if (xmlhttp.readyState==4) {
			articles = {
				'tout': {},
				'titre': {},
				'auteur': {},
				'editeur': {}
			};

			articles.tout = JSON.parse(xmlhttp.responseText);

      Object.keys(articles.tout).forEach(function(id) {
        var article = articles.tout[id];

        if(article.name.indexOf(event.target.search.value) > -1) {
					articles.titre[article.id] = article;
        }

        if(article.author.indexOf(event.target.search.value) > -1) {
          articles.auteur[article.id] = article;
        }

        if(article.editor.indexOf(event.target.search.value) > -1) {
          articles.editeur[article.id] = article;
        }
      });

      displayResults(event.target.filtre.value);
    }
  };

  xmlhttp.open('POST', 'res/search_query.php', true);
  xmlhttp.send(formData);
}

function displayResults(filtre) {
	document.getElementById('resultat').innerHTML = "";

	var table = document.createElement('table');
	table.setAttribute('class', 'tablesorter');

	var thead = document.createElement('thead');
	var tbody = document.createElement('tbody');

	var headRow = document.createElement('tr');
	var headName = document.createElement('th');
	var headAuthor = document.createElement('th');
	var headEditor = document.createElement('th');

	headName.appendChild(document.createTextNode("Titre"));
	headAuthor.appendChild(document.createTextNode("Auteur(s)"));
	headEditor.appendChild(document.createTextNode("Éditeur"));

	headRow.appendChild(headName);
	headRow.appendChild(headAuthor);
	headRow.appendChild(headEditor);
	thead.appendChild(headRow);
	table.appendChild(thead);

	Object.keys(articles[filtre]).forEach(function(id) {
		var article = articles[filtre][id];

		var tr = document.createElement('tr');
		tr.setAttribute("data-article", article.id);
		tr.setAttribute("onclick", "openArticle(this)");

		var name = document.createElement('td');
		var author = document.createElement('td');
		var editor = document.createElement('td');

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

function unsubscribe(e) {
	var xmlhttp = new getXMLHttpRequest();
	var formData = new FormData();

	var row = e.parentNode.parentNode;
	var tbody = row.parentNode;

	formData.append('f', 'unsubscribe');
	formData.append('memberNo', memberNo);
	formData.append('articleId', e.getAttribute('data-article'));

	xmlhttp.onreadystatechange = function(res) {
		if (xmlhttp.readyState==4) {
			if (xmlhttp.responseText) {
				tbody.removeChild(row);
			}
		}
	};

	xmlhttp.open('POST', 'res/article_subscription.php', true);
	xmlhttp.send(formData);
}

function subscribe(e) {
	var xmlhttp = new getXMLHttpRequest();
	var formData = new FormData();

	formData.append('memberNo', memberNo);
	formData.append('articleId', e.getAttribute('data-article'));

	if (e.getAttribute('data-state') === 'subscribed') {
		formData.append('f', 'unsubscribe');

		xmlhttp.onreadystatechange = function(res) {
			if (xmlhttp.readyState==4) {
				if (xmlhttp.responseText) {
					e.setAttribute('data-state', 'unsubscribed');
				}
			}
		};
	} else {
		formData.append('f', 'subscribe');

		xmlhttp.onreadystatechange = function(res) {
			if (xmlhttp.readyState==4) {
				if (xmlhttp.responseText) {
					e.setAttribute('data-state', 'subscribed');
				}
			}
		};
	}

	xmlhttp.open('POST', 'res/article_subscription.php', true);
	xmlhttp.send(formData);
}

function verifyCoordonates(event) {
	event.preventDefault();

  var inputNoCivic = event.target.nocivic;
  var inputCodePostal = event.target.codepostal;
  var inputTel1 = event.target.telephone1;
  var inputTel2 = event.target.telephone2;
  var inputEmail = event.target.courriel;

  var regExCodePostal = /^([a-zA-Z]\d[a-zA-z]( )?\d[a-zA-Z]\d)$/i;
  var regExTelephone = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
  var regExEmail = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;

  if (isNaN(inputNoCivic.value)) {
    inputNoCivic.setAttribute('class', 'invalid');
    return;
  }

  if (inputNoCivic.class === 'invalid') {
    inputNoCivic.removeClass('invalid');
  }

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

	var xmlhttp = new getXMLHttpRequest();
	var formData = new FormData();

  formData.append('memberNo', event.target.memberNo.value);
  formData.append('nocivic', inputNoCivic.value);
  formData.append('codepostal', inputCodePostal.value.replace(' ', ''));
  formData.append('telephone1', inputTel1.value.replace('-', '').replace('(', '').replace(' ', '').replace('.', ''));
  formData.append('idtel1', event.target.idtel1.value);
  formData.append('telephone2', inputTel2.value.replace('-', '').replace('(', '').replace(' ', '').replace('.', ''));
  formData.append('idtel2', event.target.idtel2.value);
  formData.append('courriel', inputEmail.value);
  formData.append('rue', event.target.rue.value);
  formData.append('app', event.target.app.value || '');
  formData.append('ville', event.target.ville.value);
  formData.append('province', event.target.province.value);

  xmlhttp.onreadystatechange = function(res) {
    if (xmlhttp.readyState == 4) {
      if (xmlhttp.responseText) {
        document.location.href = 'index.php';
      }
    }
  }

  xmlhttp.open('POST', 'res/mise_a_jour.php', true);
  xmlhttp.send(formData);
}

function eventHandlers() {
	window.addEventListener('scroll', deleteTooltip);

	document.getElementById('search').addEventListener('search', function(event) {
		document.location.href = 'recherche.php?r=' + event.target.value;
	});

	var desuet = document.getElementsByClassName('desuet');
	for(noDesuet = 0; noDesuet < desuet.length; noDesuet++) {
	  desuet[noDesuet].addEventListener('mouseover', createTooltip);
	  desuet[noDesuet].addEventListener('mouseout', deleteTooltip);
	}

	if(document.getElementById('search-form')) {
		document.getElementById('search-form').addEventListener('submit', search);

		var filtres = document.forms['search-form'].elements["filtre"];
		for(var i = 0; i < filtres.length; i++) {
			filtres[i].addEventListener("change", function(event) {
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
	  $(".tablesorter").tablesorter();
	});
}

/*===========
| EXECUTION |
===========*/
var articles;
var tooltip;
var r = getParameterByName('r');

slideoutMenu();
eventHandlers();

if(r && document.getElementById('recherche') &&
		document.getElementById('btn-recherche')) {
			
	document.getElementById('recherche').value = r;
	document.getElementById('btn-recherche').click();
}

sortTables();
