function displayItem(item) {
  const inStock = item.copies.filter(copy => copy.isAdded);
  console.log(inStock);
  const title = document.getElementById('title');
  title.appendChild(document.createTextNode(item.name));

  if (memberNo) {
    const subscribeBtn = document.createElement('i');
    subscribeBtn.setAttribute('class', 'oi');
    subscribeBtn.setAttribute('data-item', item.id);
    subscribeBtn.setAttribute('data-state', 'subsribed');
    subscribeBtn.addEventListener('click', subscribe);

    title.appendChild(subscribeBtn);
  }

  document.getElementById('author').innerText = item.authorString;
  document.getElementById('editor').innerText = item.editor;
  document.getElementById('edition').innerText = item.edition;
  document.getElementById('publication').innerText = item.publication;
  document.getElementById('ean13').innerText = item.ean13;

  if (inStock.length > 0) {
    const avg = Math.round(inStock.reduce((acc, cur) => cur.price + acc, 0) / inStock.length);
    document.getElementById('quantity').innerText = `Nous possédons ${inStock.length} exemplaire(s) en stock de cet article et le prix moyen de vente est de ${avg} $`
  } else {
    document.getElementById('quantity').innerText = `Nous ne possédons pas d'exemplaire en stock pour cet article. Vous pouvez le suivre pour être informer d'un éventuel approvisionnement.`
  }
}

const itemId = getParams().article;
const url = `http://localhost/blu/api/src/server/index.php/item/${itemId}`;
request('GET', url, null, (err, res) => {
  if (res) {
    displayItem(new Item(res));
  }
});