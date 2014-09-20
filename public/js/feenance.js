var feenance = angular.module("feenance", ['ngResource', 'ngRoute', 'ui.bootstrap']);

feenance.controller('FeenanceController', function($scope) {
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

  $scope.$on('newTransactions', function($event, $transactions) {
    console.log("newTransactions in FeenanceController");
    $scope.$broadcast('addTransactions', $transactions);
  });

  $scope.$on('selectTransaction', function($event, $transaction) {
    $scope.$broadcast('editTransaction', $transaction);
  });


  $scope.toggleDebug = function() {
    $scope.debug = !$scope.debug;
    $scope.$broadcast('setDebug', $scope.debug);
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