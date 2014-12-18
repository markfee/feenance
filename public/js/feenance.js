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

feenance.directive('optionsClass', function ($parse) {
  return {
    require: 'select',
    link: function(scope, elem, attrs, ngSelect) {
      // get the source for the items array that populates the select.
      var optionsSourceStr = attrs.ngOptions.split(' ').pop(),
      // use $parse to get a function from the options-class attribute
      // that you can use to evaluate later.
          getOptionsClass = $parse(attrs.optionsClass);

      scope.$watch(optionsSourceStr, function(items) {
        // when the options source changes loop through its items.
        angular.forEach(items, function(item, index) {
          // evaluate against the item to get a mapping object for
          // for your classes.
          var classes = getOptionsClass(item),
          // also get the option you're going to need. This can be found
          // by looking for the option with the appropriate index in the
          // value attribute.
              option = elem.find('option[value=' + index + ']');

          // now loop through the key/value pairs in the mapping object
          // and apply the classes that evaluated to be truthy.
          angular.forEach(classes, function(add, className) {
            if(add) {
              angular.element(option).addClass(className);
            }
          });
        });
      });
    }
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