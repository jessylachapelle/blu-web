function slideoutMenu() {
  var menuButton = document.getElementById('menu-button');
  var panel = document.getElementsByTagName('main')[0];
  var header = document.getElementsByTagName('header')[0];

  var slideout = new Slideout({
    panel: panel,
    menu: document.getElementById('menu'),
    padding: 1,
    tolerance: 70
  });

  menuButton.addEventListener('click', slideout.toggle);
  header.addEventListener('click', slideout.close, true);
  panel.addEventListener('click', slideout.close);
}

function request (method, path, data, callback) {
  var apiUrl = 'http://localhost/blu-api/src/server/index.php';
  var xhttp = new XMLHttpRequest();

  var url = apiUrl + path;
  var postData;

  xhttp.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      try {
        callback(null, JSON.parse(this.responseText))
      } catch (err) {
        callback(null, this.responseText);
      }
    } else if (this.readyState === 4) {
      callback({ status: this.status, message: this.responseText });
    }
  };

  if (method === 'GET' || method === 'DELETE') {
    url += '?' + Object.keys(data || {}).map(function(key) {
      return key + '=' + data[key];
    }).join('&');
  } else {
    postData = JSON.stringify(data || {});
  }

  xhttp.open(method, url, true);
  xhttp.send(postData);
}

function createElement(tag, attributes, events, parent) {
  var element = document.createElement(tag);

  Object.keys(attributes || {}).forEach(function(key) {
    if (key === 'data') {
      Object.keys(attributes[key]).forEach(function(dataKey) {
        element.setAttribute('data-' + dataKey, attributes.data[dataKey]);
      });
    } else if (Array.isArray(attributes[key])) {
      element.setAttribute(key, attributes[key].join(' '));
    } else {
      element.setAttribute(key, attributes[key]);
    }
  });

  Object.keys(events || {}).forEach(function(key) {
    element.addEventListener(key, events[key]);
  });

  if (parent) {
    parent.appendChild(element);
  }

  return element;  
}

function openItem(event) {
  document.location.href = 'article.php?article=' + event.currentTarget.dataset.item;
}

function populateTable(tableBody, columns, data, extraConfig) {
  data.forEach(function(row) {
    var tr = document.createElement('tr');
    
    if (extraConfig) {
      extraConfig(tr, row);
    }

    columns.forEach(function(column) {
      var td = document.createElement('td');
      td.innerText = row[column];
      tr.appendChild(td);
    });

    tableBody.appendChild(tr);
  });
}

function getParams() {
  return location.search.replace('\?', '').split('&').reduce(function (params, param) {
    if (param) {
      var keyValue = param.split('=');
      params[keyValue[0]] = decodeURIComponent(keyValue[1]);
    }

    return params;
  }, {});
}

/*===========
| EXECUTION |
===========*/
var tooltip;

if (window.Slideout) {
	slideoutMenu();
}

document.getElementById('search').addEventListener('search', function(event) {
	event.preventDefault();
	document.location.href = 'recherche.php?r=' + event.target.value;
});

$(document).ready(function() {
	$('.tablesorter').tablesorter();
});
