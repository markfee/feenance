var feenance = angular.module("feenance", ['ngResource', 'ngRoute']);

feenance.factory('Notifier', function () {
  var observerCallbacks = {};
  return {
    onChange: function (name, callback) {
      if (undefined === observerCallbacks[name] )
        observerCallbacks[name] = [];
      observerCallbacks[name].push(callback);
    },
    notify: function (name, param) {
      if (undefined === observerCallbacks[name] ) return; // nobody is listening :(
      angular.forEach(observerCallbacks[name], function (callback) {
        callback(param);
      });
    }
  };
});

feenance.factory('CurrentAccount', function(Notifier) {
  var account = {};
  function set(newAccount) {
    account = newAccount;
    Notifier.notify('CurrentAccount', account);
  };
  return {
    set: set
    , get: function () {      return account;    }
    , onChange: function (callback) { Notifier.onChange('CurrentAccount', callback); callback(account);    }
  }
});