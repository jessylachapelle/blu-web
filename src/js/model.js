class Author {
  constructor(data) {
    const author = data || {};
    this.id = author.id || 0;
    this.firstName = author.firstName || '';
    this.lastName = author.lastName || '';
  }

  toString() {
    return this.firstName !== '' ? this.lastName + ' ' + this.firstName : this.lastName;
  }
}

class Item {
  constructor(data) {
    const item = data || {};
    this.id = item.id || 0;
    this.name = item.name || '';
    this.publication = item.publication || '';
    this.edition = item.edition || '';
    this.editor = item.editor || '';
    this.subject = item.subject || {};
    this.isBook = item.isBook || false;
    this.ean13 = item.ean13;
    this.author = item.author ? item.author.map(function (author) {
      return new Author(author);
    }) : [];
    this.status = item.status || {};

    if (Array.isArray(item.copies)) {
      this.copies = item.copies ? item.copies.map(function (copy) {
        return new Copy(copy);
      }) : [];
    } else {
      this.copies = item.copies;
    }
  }

  getStatus() {
    if (this.isRemoved) {
      return Item.STATUS.REMOVED;
    }

    if (this.isOutdated) {
      return Item.STATUS.OUTDATED;
    }

    return Item.STATUS.VALID;
  }

  getStatusString() {
    const status = {
      OUTDATED: 'Désuet',
      REMOVED: 'Retiré',
      VALID: 'Valide',
    };

    return status[this.getStatus()];
  }

  get isInStock() {
    return this.copies.filter(function (copy) {
      return copy.isAdded;
    }).length > 0;
  }

  get isValid() {
    return !this.status.REMOVED && !this.status.OUTDATED;
  }

  get isOutdated() {
    return !this.status.REMOVED && this.status.OUTDATED;
  }

  get isRemoved() {
    return !!this.status.REMOVED;
  }

  get authorString() {
    return this.author.length ? this.author.map(function (author) {
      return author.toString();
    }).join(', ') : '';
  }

  static get STATUS() {
    return {
      VALID: 'VALID',
      OUTDATED: 'OUTDATED',
      REMOVED: 'REMOVED',
    };
  }
}


class Transaction {
  constructor(data) {
    const transaction = data || {};
    const member = transaction.member || transaction.parent;

    this.code = transaction.code;
    this.date = transaction.date ? new Date(transaction.date) : null;
    this.member = member ? new Member(member) : null;
  }

  static get TYPES() {
    return {
      ADD: 'ADD',
      AJUST_INVENTORY: 'AJUST_INVENTORY',
      DONATE: 'DONATE',
      PAY: 'PAY',
      RESERVE: 'RESERVE',
      SELL: 'SELL',
      SELL_PARENT: 'SELL_PARENT',
      get ALL_SELL() {
        return [this.SELL, this.SELL_PARENT];
      },
    };
  }
}


class Copy {
  constructor(data) {
    const copy = data || {};
    this.id = copy.id || 0;
    this.price = +copy.price || 0;
    this.transaction = (copy.transaction || []).map(function (t) {
      return new Transaction(t);
    });
    this.item = copy.item ? new Item(copy.item) : null;
    this.member = copy.member ? new Member(copy.member) : null;
  }

  get title() {
    return this.item.name;
  }

  get priceString() {
    return this.price + '$';
  }

  get status() {
    const transactions = {};

    this.transaction.forEach(function (transaction) {
      transactions[transaction.code] = true;
    });

    if (transactions.PAY) {
      return Copy.STATUS.PAID;
    }

    if (transactions.SELL || transactions.SELL_PARENT) {
      return Copy.STATUS.SOLD;
    }

    if (transactions.RESERVE) {
      return Copy.STATUS.RESERVED;
    }

    return Copy.STATUS.ADDED;
  }

  get dateAdded() {
    return this.transaction.filter(function (t) {
      return t.code === Transaction.TYPES.ADD;
    })[0].date.toLocaleDateString();
  }

  get dateSold() {
    const transaction = this.transaction.filter(function (t) {
      return Transaction.TYPES.ALL_SELL.indexOf(t.code) > -1;
    })[0];
    return transaction ? transaction.date.toLocaleDateString() : '';
  }

  get datePaid() {
    const transaction = this.transaction.filter(function (t) {
      return t.code === Transaction.TYPES.PAY;
    })[0];
    return transaction ? transaction.date.toLocaleDateString() : '';
  }

  get dateReserved() {
    const transaction = this.transaction.filter(function (t) {
      return t.code === Transaction.TYPES.RESERVE;
    })[0];
    return transaction ? transaction.date.toLocaleDateString() : '';
  }

  get isDonated() {
    return !!this.transaction.filter(function (t) {
      return t.code === Transaction.TYPES.DONATE;
    })[0];
  }

  get isSold() {
    return this.status === Copy.STATUS.SOLD;
  }

  get isAdded() {
    return this.status === Copy.STATUS.ADDED;
  }

  get isPaid() {
    return this.status === Copy.STATUS.PAID;
  }

  get isReserved() {
    return this.status === Copy.STATUS.RESERVED;
  }

  static get STATUS() {
    return {
      ADDED: 'ADDED',
      DONATED: 'DONATED',
      PAID: 'PAID',
      RESERVED: 'RESERVED',
      SOLD: 'SOLD',
    };
  }
}


class City {
  constructor(data) {
    const city = data || {};
    this.id = city.id || 0;
    this.name = city.name || '';
    this.state = {
      code: city.state ? city.state.code || '' : '',
      name: city.state ? city.state.name || '' : '',
    };
  }
}


class Phone {
  constructor(data) {
    const phone = data || {};
    this.id = phone.id || 0;
    this.number = phone.number || '';
    this.note = phone.note || '';
  }

  toString() {
    const number = this.number.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    const hasNote = this.note && this.note !== '';
    return hasNote ? number + '(' + this.note + ')' : number;
  }
}


class Account {
  constructor(data) {
    const account = data || {};

    this.registration = new Date('2014-01-20T00:00:00');

    this.registration = new Date((account.registration || '').replace(' ', 'T'));
    this.lastActivity = new Date((account.lastActivity || '').replace(' ', 'T'));
    this.comment = account.comment ? account.comment.map(function (comment) {
      return new Comment(comment);
    }) : [];
    this.copies = account.copies ? account.copies.map(function (copy) {
      return new Copy(copy);
    }) : [];
    this.transfers = account.transfers || [];
    this.itemFeed = account.itemFeed || [];
  }

  get deactivationDate() {
    const date = new Date(this.lastActivity.toISOString());
    date.setFullYear(this.lastActivity.getFullYear() + 1);
    return date;
  }

  get isActive() {
    return new Date() < this.deactivationDate;
  }

  getAddedCopies() {
    return this.copies.filter(function (copy) {
      return copy.isAdded || copy.isReserved;
    });
  }

  getSoldCopies() {
    return this.copies.filter(function (copy) {
      return copy.isSold;
    });
  }

  getPaidCopies() {
    return this.copies.filter(function (copy) {
      return copy.isPaid;
    });
  }
}

class Member {
  constructor(data) {
    const member = data || {};
    this.no = member.no || 0;
    this.firstName = member.firstName || '';
    this.lastName = member.lastName || '';
    this.email = member.email || '';
    this.isParent = member.isParent || false;
    this.address = member.address || '';
    this.zip = member.zip || '';
    this.city = new City(member.city);
    this.account = new Account(member.account);
    this.phone = (member.phone || []).map(function (phone) {
      return new Phone(phone);
    });

    while (this.phone.length < 2) {
      this.phone.push(new Phone());
    }
  }

  get name() {
    return this.firstName + ' ' + this.lastName;
  }

  get contactInfo() {
    const data = {
      address: this.address,
      city: this.city.name + '(' + this.city.state.name + ')',
      zip: this.zip.replace(/(.{3})(.{3})/, '$1 $2'),
      phone: this.phone.filter(function (phone) {
        return phone.number;
      }).map(function (phone) {
        return phone.toString();
      }).join('\n'),
      email: this.email,
    };

    return Object.keys(data).filter(function (key) {
      return data[key];
    }).map(function (key) {
      return data[key];
    }).join('\n');
  }
}
