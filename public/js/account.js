feenance.factory('AccountCollection', function(Notifier, AccountsApi, $filter) {
  var collection = { data: [] };
  var $_PLEASE_SELECT =  {id: null, name: "<Please select an account>"};
  collection.data[0] = $_PLEASE_SELECT;

  var promises = {};

  var accounts = AccountsApi.get({},
    function()
    {
      angular.extend(collection.data, accounts.data);
      collection.data.splice(0, 0, $_PLEASE_SELECT);
      _updatePromises();
    }
  );

  /*
   * promises are a set of objects that will contain the index of an account, once the accounts are returned
   * from the ajax call.
   * This private method is called post ajax return to populate all of the promises
   */
  function _updatePromises() {
    angular.forEach(collection.data,
      function(value, key)
      {
        if (promises[value.id] != undefined) {
          promises[value.id].index = key;
        }
      }
    );
  }

  /*
   * This private method sets a promise and populates it if data is available,
   * otherwise it waits until the promises are fetched and _updatePromises is called
   */
  function _setPromise(promise, id) {
    angular.forEach(collection.data,
      function(value, key)
      {
        if (value.id == id) {
          promise.index = key;
        }
      }
    );
    return promise;
  }

  return {
    collection:
      function ()
      {
        return collection.data;
      },
    getPromisedIndex:
      function (id)
      {
        if (promises[id] != undefined) {
          return promises[id];
        }
        promises[id] = {index:0};
        return _setPromise(promises[id], id);
      },
    add:
      function(newAccount)
      {
        collection.data.push(newAccount);
        return newAccount;
      }
  }

});

feenance.controller('AccountController', function($scope, $transclude, AccountsApi, AccountCollection) {
  $scope.accounts   = AccountCollection.collection();
  $scope.selected   = null;
  $scope.title = "Account";
  $scope.name = "account_id";
  $scope.emitMessage = "Account";
  $scope.optional = false;
  $scope.editing = false;
  var rollback = null;

  $scope.cancel = function () {
    angular.extend($scope.selected, rollback);
    $scope.editing = false;
  };

  $scope.edit = function () {
    rollback = angular.copy($scope.selected);
    $scope.editing = true;
  };

  $scope.new = function() {
    rollback = $scope.selected;
    $scope.selected = new AccountsApi();
    $scope.editing=true;
  };

  $scope.save = function () {
    $scope.selected.$save(function(response) {
      $scope.selected = AccountCollection.add(response);
      rollback = null;
    });
  };

  $scope.update = function () {
    AccountsApi.update({id:$scope.selected.id}, $scope.selected, function(response) {
      angular.extend($scope.selected, response);
      $scope.editing = false;
      rollback = null;
    });
  };

  $transclude(function(clone, scope) {
    $scope.title = clone.html();
    if ($scope.title == undefined)  $scope.title = "Account";
  });


  $scope.$on('setTransfer', function (event, $newAccount) {
    if ($scope.emitMessage != "Transfer") {
      return;
    }
    console.log("received: setTransfer in " + $scope.title);
    if ($newAccount.id == undefined) {
      selectAccount($newAccount);
    }
    else {
      selectAccount($newAccount.id);
    }
  });

  $scope.selectAccount = function(accountId) {
    $scope.selected = AccountCollection.getPromisedIndex(accountId);
    return $scope.selected;
  }

  $scope.$watch('selected.index',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        $scope.selected = $scope.accounts[new_val];
      }
    }
  );

});

feenance.directive('accountSelector', function() {
  return {
    restrict: 'E'
    , transclude: true
    ,  scope: {
      selected: "=ngModel"
      , accountId: "=" // remember account_id in markup accountId in directive / controller
      , name: "@"
    }
    , templateUrl: '/view/account_selector.html'
    , link: function (scope, element, attr) {
      scope.emitMessage =  attr.emitMessage ? attr.emitMessage : scope.emitMessage;
      scope.optional = attr.optional ? true : false;
      if (scope.accountId) {
        scope.selectAccount(scope.accountId);
      }
    }
    , controller: "AccountController"
  };
});

feenance.directive('myaccountSelector', function() {
  return {
    restrict: 'E'
    , transclude: true
    ,  scope: {
      accountId: "=ngModel"
//      , accountId: "=" // remember account_id in markup accountId in directive / controller
      , name: "@"
    }
    , templateUrl: '/view/account_selector.html'
    , link: function (scope, element, attr) {
      scope.emitMessage =  attr.emitMessage ? attr.emitMessage : scope.emitMessage;
      scope.optional = attr.optional ? true : false;
      if (scope.accountId) {
        scope.selectAccount(scope.accountId);
      }
      scope.$watch('scope.selected.id', function(new_val, old_val) {
        scope.accountId = new_val;
      });
    }
    , controller: "AccountController"
  };
});