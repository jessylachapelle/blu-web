function request (method, url, data, callback) {
  const xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      try {
        callback(null, JSON.parse(this.responseText))
      } catch (err) {
        callback(null, this.responseText);
      }
    } else if (this.readyState === 4) {
      callback({ status: this.status, message: this.responseText });
    }
  };
  xhttp.open(method, url, true);
  xhttp.send(data || {});
}

function openItem(event) {
  document.location.href = `article.php?article=${event.currentTarget.dataset.item}`;
}

function populateTable(tableBody, columns, data, extraConfig) {
  data.forEach((row) => {
    const tr = document.createElement('tr');
    
    if (extraConfig) {
      extraConfig(tr, row);
    }

    columns.forEach((column) => {
      const td = document.createElement('td');
      td.innerText = row[column];
      tr.appendChild(td);
    });

    tableBody.appendChild(tr);
  });
}

function getParams() {
  return location.search.replace('\?', '').split('&').reduce(function (params, param) {
    if (param) {
      const keyValue = param.split('=');
      params[keyValue[0]] = keyValue[1];
    }

    return params;
  }, {});
}