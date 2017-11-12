function getFormData() {
  const data = new Member(member);

  data.address = document.getElementById('address').value;
  data.zip = document.getElementById('zip').value.replace(/\W/g, '');
  data.city.name = document.getElementById('city').value;
  data.city.state.code = document.getElementById('state').value;
  data.email = document.getElementById('email').value;
  data.phone = [];

  for (let i = 1; i <= 2; i++) {
    if (document.getElementById(`phone${i}`).value) {
      data.phone.push(new Phone({
        number: document.getElementById(`phone${i}`).value.replace(/\D/g, ''),
        note: document.getElementById(`note${i}`).value,
      }))
    }
  }

  return data;
}

function setCopyTableRowAttributes(tr, row) {
  tr.setAttribute('data-item', row.item.id);
  tr.addEventListener('click', openItem);

  if (!row.item.status.VALID && row.isAdded) {
    tr.setAttribute('class', 'outdated');
    tr.addEventListener('mouseover', createTooltip);
    tr.addEventListener('mouseout', deleteTooltip);
  }
}

function renewAccount() {
  request('GET', `/member/${memberNo}/renew`, null, (err) => {
    if (!err) {
      renewButton.innerHTML = 'Compte renouvelé';
      renewButton.setAttribute('disabled', 'disabled');
      renewButton.setAttribute('class', 'desactive');

      member.account.lastActivity = new Date();
      document.getElementById('lastActivity').innerText = member.account.lastActivity.toLocaleDateString();
      document.getElementById('deactivation').innerText = member.account.deactivationDate.toLocaleDateString();
    }
  });
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

function closeOverlay() {
  document.getElementById('overlay').style.display = 'none';  
}

let member;

request('GET', `/member/${memberNo}`, null, (err, res) => {
  if (res) {
    member = new Member(res);
    displayMember(member);
  }
});

window.addEventListener('scroll', deleteTooltip);

const closable = document.getElementsByClassName('close');

for (let i = 0; i < closable.length; i++) {
  closable[i].addEventListener('click', closeOverlay);
}

document.getElementById('updateInfo').addEventListener('click', (event) => {
  event.preventDefault();
  document.getElementById('address').value = member.address;
  document.getElementById('city').value = member.city.name;
  document.getElementById('zip').value = member.zip;
  document.getElementById('state').value = member.city.state.code;
  document.getElementById('phone1').value = member.phone[0].number;
  document.getElementById('note1').value = member.phone[0].note; 
  document.getElementById('phone2').value = member.phone[1].number;
  document.getElementById('note2').value = member.phone[1].note; 
  document.getElementById('email').value = member.email;
  document.getElementById('overlay').style.display = 'block';
});

document.getElementById('updateForm').addEventListener('submit', (event) => {
  event.preventDefault();
  const data = getFormData();

  if (!/.@./.test(data.email)) {
    alert('Courriel invalide');
  }

  request('POST', `/member/${memberNo}`, data, (err, res) => {
    if (err) {
      console.log(err);
      alert('Une erreur c\'est produite. Veuillez réessayer plus tard');
      closeOverlay();      
      return;
    }

    member = data;
    document.getElementById('contactInfo').innerText = member.contactInfo;
    closeOverlay();
  });
});
