function search(event) {
  event.preventDefault();
  var value = event.target.search.value;
  var url = window.location.origin + window.location.pathname;
  var data = {
    search: value,
  };
  
  // window.location.replace(url + '?r=' + value);

  request('GET', '/item', data, function (err, res) {
  	if (res && Array.isArray(res)) {
    	displayResults((res).map(function (item) {
    		return new Item(item);
      }));
		}
  });
}

function displayResults(items) {
	document.getElementById('resultat').innerHTML = '';

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

	items.forEach(function (item) {
		var tr = document.createElement('tr');
		tr.setAttribute('data-item', item.id);
		tr.addEventListener('click', openItem);

		var name = document.createElement('td');
		var author = document.createElement('td');
		var editor = document.createElement('td');

		name.appendChild(document.createTextNode(item.name));
		author.appendChild(document.createTextNode(item.authorString));
		editor.appendChild(document.createTextNode(item.editor));

		tr.appendChild(name);
		tr.appendChild(author);
		tr.appendChild(editor);

		tbody.appendChild(tr);
	});

	table.appendChild(tbody);
	document.getElementById('resultat').appendChild(table);
	sortTables();
}

document.getElementById('search-form').addEventListener('submit', search);

var searchParam = getParams().r;

if (searchParam) {
	document.getElementById('recherche').value = searchParam;
	document.getElementById('btn-recherche').click();
}