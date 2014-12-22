var feenance = angular.module("feenance", ['ngResource', 'ngRoute', 'ui.bootstrap']);

feenance.filter('range', function() {
  return function(input, min, max) {
    min = parseInt(min);
    max = parseInt(max);
    for (var i=min; i<=max; i++)
      input.push(i);
    return input;
  };
});

feenance.filter('isoDate', function() {
  return function(input) {
    return input.substr(0,10);
  };
});

feenance.controller('FeenanceController', function($scope, $templateCache) {
  $scope.debug = false;

  $scope.$on('updatedAccount', function($event, account) {
    console.log("received updatedAccount in FeenanceController");
    console.log("broadcasting setAccount");
    $scope.$broadcast('setAccount', account);
  });

  $scope.$on('updatedTransfer', function($event, account) {
    console.log("received updatedTransfer in FeenanceController");
    console.log("broadcasting setTransfer");
    $scope.$broadcast('setTransfer', account);
  });

  $scope.$on('refreshTransactions', function($event, $transactions) {
    console.log("refreshTransactions in FeenanceController");
//    $scope.$broadcast('addTransactions', $transactions);
    $scope.$broadcast('setAccount', $scope.account);
  });

  $scope.$on('selectTransaction', function($event, $transaction) {
    $scope.$broadcast('editTransaction', $transaction);
  });

  $scope.$on('BankStringClicked', function($event, bank_string_id) {
    $scope.$broadcast('editMap', bank_string_id);
  });

  $scope.toggleDebug = function() {
    $scope.debug = !$scope.debug;
    $scope.$broadcast('setDebug', $scope.debug);
  };

  $scope.clearCache = function() {
    $scope.clearCache = function() {
      $templateCache.removeAll();
    }
  };


});

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

feenance.factory('Paginator', function () {
  return {
    nextPage: function(response) {
      try {
        return response.paginator.next;
      } catch(e) {
        return;
      }
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