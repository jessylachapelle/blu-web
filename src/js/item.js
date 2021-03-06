function handleSubscription(event) {
  const star = event.currentTarget;
  const state = star.getAttribute('data-state') === 'subscribed' ? 'unsubscribe' : 'subscribed';
  const method = star.getAttribute('data-state') === 'subscribed' ? 'DELETE' : 'GET';
  const id = star.getAttribute('data-item');

  request(method, `/member/${memberNo}/subscription/item/${id}`, null, (err) => {
    if (!err) {
      star.setAttribute('data-state', state);
    }
  });
}

function displaySubscription(id) {
  request('GET', `/member/${memberNo}/isSubscribed/item/${id}`, null, (err, res) => {
    if (res) {
      const state = res.isSubscribed ? 'subscribed' : 'unsubscribed';
      const attributes = {
        class: 'oi',
        data: {
          glyph: 'star',
          item: id,
          state,
        },
      };
      const events = {
        click: handleSubscription,
      };

      createElement('i', attributes, events, document.getElementById('title'));
    }
  });
}

function displayItem(item) {
  document.getElementById('title').appendChild(document.createTextNode(item.name));
  document.getElementById('author').innerText = item.authorString;
  document.getElementById('editor').innerText = item.editor;
  document.getElementById('edition').innerText = item.edition;
  document.getElementById('publication').innerText = item.publication;
  document.getElementById('ean13').innerText = item.ean13;
  
  if (memberNo) {
    displaySubscription(item.id, title);
  }

  if (item.copies.quantity > 0) {
    document.getElementById('quantity').innerText = `Nous possédons ${iten.copies.quantity} exemplaire(s) en stock de cet article et le prix moyen de vente est de ${items.copies.averagePrice} $`
  } else {
    document.getElementById('quantity').innerText = `Nous ne possédons pas d'exemplaire en stock pour cet article. Vous pouvez le suivre pour être informé.e d'un éventuel approvisionnement.`
  }
}

const itemId = getParams().article;

request('GET', `/item/${itemId}`, null, (err, res) => {
  if (err) {
    alert('Une erreur est survenue en récupérant les informations de l\'ouvrage. Si vous continuez à rencontrer des erreurs veuillez nous en faire part.');
    return;
  }
  
  displayItem(new Item(res));
});