function search(event) {
	event.preventDefault();
  const data = {
    search: event.target.search.value,
  };

  
  request('GET', '/item', data, (err, res) => {
    if (res) {
      displayResults(res.map(item => new Item(item)));
    }
  });
}

function displayResults(items) {
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

	items.forEach((item) => {
		const tr = document.createElement('tr');
		tr.setAttribute('data-item', item.id);
		tr.addEventListener('click', openItem);

		const name = document.createElement('td');
		const author = document.createElement('td');
		const editor = document.createElement('td');

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

const searchParam = getParams().r;

if (searchParam) {
	document.getElementById('recherche').value = searchParam;
	document.getElementById('btn-recherche').click();
}