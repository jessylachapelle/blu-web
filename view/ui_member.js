function setCopyTableRowAttributes(tr, row) {
  tr.setAttribute('data-item', row.id);
  tr.addEventListener('click', openItem);

  if (row.inStock) {
    tr.setAttribute('class', 'enstock');
  }
}

function renewAccount() {
  request('GET', `http://localhost/blu/api/src/server/index.php/member/${memberNo}/renew`, null, (err) => {
    if (!err) {
      renewButton.innerHTML = 'Compte renouvelé';
      renewButton.setAttribute('disabled', 'disabled');
      renewButton.setAttribute('class', 'desactive');
    }
  });
}

function displayMember (member) {
  const isActive = member.account.isActive;
  const deactivation = member.account.deactivationDate.toLocaleDateString();
  const copyTables = {
    added: {
      columns: ['title', 'dateAdded', 'priceString'],
      data: member.account.getAddedCopies(),
    },
    sold: {
      columns: ['title', 'dateAdded', 'dateSold', 'priceString'],
      data: member.account.getSoldCopies(),
    },
    paid: {
      columns: ['title', 'dateAdded', 'dateSold', 'datePaid', 'priceString'],
      data: member.account.getPaidCopies(),
    },
  };

  if (!isActive) {
    document.getElementById('deactivationDate').innerText = deactivation;
    document.getElementById('deactivationBanner').style.display = 'block';
  } else {
    if (copyTables.sold.data.length) {
      const copiesSold = copyTables.sold.data;
      document.getElementById('soldQty').innerText = copiesSold.length;
      document.getElementById('soldAmount').innerText = copiesSold.reduce((total, copy) =>
        total + copy.price, 0
      );    
      document.getElementById('soldBanner').style.display = 'block';
    }

    const renewButton = document.createElement('button');
    renewButton.id = 'renewButton';
    renewButton.innerText = 'Renouveler mon compte';
    renewButton.addEventListener('click', renewAccount);
    document.getElementById('actions').appendChild(renewButton);
  }

  document.getElementById('name').innerText = `Bonjour ${member.name}`;
  document.getElementById('registration').innerText = member.account.registration.toLocaleDateString();
  document.getElementById('lastActivity').innerText = member.account.lastActivity.toLocaleDateString();
  document.getElementById('deactivation').innerText = deactivation;
  document.getElementById('contactInfo').innerText = member.contactInfo;

  if (member.account.itemFeed.length) {
    const tableBody = document.getElementById('itemFeedBody');
    const columns = ['title', 'inStock'];

    populateTable(tableBody, columns, member.account.itemFeed, (tr, row) => {
      tr.setAttribute('data-item', row.id);
      tr.addEventListener('click', openItem);

      if (row.inStock) {
        tr.setAttribute('class', 'enstock');
      }
    });

    document.getElementById('itemFeed').style.display = 'block';
  }

  Object.keys(copyTables).forEach((key) => {
    const data = copyTables[key].data;
    const columns = copyTables[key].columns;

    if (data.length) {
      const total = data.reduce((acc, copy) => acc + copy.price, 0);
      const tableBody = document.getElementById(`${key}Body`);
      populateTable(tableBody, columns, data, setCopyTableRowAttributes);
  
      document.getElementById(`${key}Stat`).innerText = `${data.length} articles, ${total} $`;
      document.getElementById(key).style.display = 'block';
    }
  });
}

const url = `http://localhost/blu/api/src/server/index.php/member/${memberNo}`;
request('GET', url, null, (err, res) => {
  if (res) {
    displayMember(new Member(res));
  }
});