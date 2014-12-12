feenance.factory('Collection', function(Notifier, AccountsApi, $filter) {
  var collection = { data: [] };
  var promises = {};
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

  return function ($initialText) {
    if ($initialText) {
      collection.data[0] = {id: null, name: $initialText};
    }

    this.getData = function()
    {
      return collection.data;
    };
    this.setData = function(data, $initialText)
    {
      angular.extend(collection.data, data);
      if ($initialText) {
        collection.data.splice(0, 0, {id: null, name: $initialText});
      }
      _updatePromises();
    };
    this.add = function(record)
    {
      collection.data.push(record);
      return record;
    };
    this.getPromisedIndex = function (id)
    {
      if (promises[id] != undefined) {
        return promises[id];
      }
      promises[id] = {index:0};
      return _setPromise(promises[id], id);
    },
    this.getItemAtIndex = function (index)
    {
      return collection.data[index];
    }
  };
});

feenance.factory('AccountCollection', function(AccountsApi, Collection) {
  var collection = new Collection("..fetching accounts");
  var $_PLEASE_SELECT =   {id: null, name: "<Please select an account>"};
  var accounts = AccountsApi.get({}, function()
  {
    collection.setData(accounts.data, "<Please select an account>");
  });

  return {
    collection: function ()
    {
      return collection.getData();
    },
    getPromisedIndex: function (id)
    {
      return collection.getPromisedIndex(id);
    },
    add: function(newAccount)
    {
      return collection.add(newAccount);
    },
    getItemAtIndex: function (index)
    {
      return collection.getItemAtIndex(index);
    }
  };
});

feenance.controller('AccountController', function($scope, $transclude, AccountsApi, AccountCollection, CollectionSelection) {
  var collectionSelection = new CollectionSelection(AccountCollection, AccountsApi, $scope, "accounts", "account_id");

  $scope.title = "Account";
  $scope.name = "account_id";
  $scope.editing = false;
  var rollback = null;

  $scope.getId = function() {
    return $scope.account_id;
  }

  $scope.cancel = function () {
    $scope.collectionSelection.rollback();
    $scope.editing = false;
  };

  $scope.edit = function () {
    $scope.collectionSelection.beginEditing();
    $scope.editing = true;
  };

  $scope.new = function() {
    $scope.collectionSelection.beginEditingNewItem(new AccountsApi());
    $scope.editing=true;
  };

  $scope.save = function () {
    $scope.collectionSelection.saveItem();
  };

  $scope.update = function () {
    $scope.collectionSelection.saveItem();
  };

  if ($transclude != undefined) {
    $transclude(function (clone, scope) {
      $scope.title = clone.html();
      if ($scope.title == undefined)  $scope.title = "Account";
    });
  }

  $scope.selectAccount = function(accountId) {
    return $scope.collectionSelection.selectItem(accountId);
  }
});

feenance.directive('accountSelector', function() {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      selected: "=ngModel"
      , accountId: "=" // remember account_id in markup accountId in directive / controller
      , name: "@"
    }
    , templateUrl: '/view/account_selector.html'
    , link: function (scope, element, attr) {
      if (scope.accountId) {
        scope.selectAccount(scope.accountId);
      }
    }
    , controller: "AccountController"
  };
});

feenance.directive('accountIdSelector', function() {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      account_id: "=ngModel",
      name: "@"
    }
    , templateUrl: '/view/account_selector.html'
    , link: function (scope, element, attr) {
      if (scope.account_id) {
        scope.selectAccount(scope.account_id);
      }
    }
    , controller: "AccountController"
  };
});

feenance.directive('accountName', function() {
  return {
    restrict: 'E'
    ,  scope: {
      account_id: "=ngModel",
      ngModel: "="
    }
    , template: '{{selected.name}}'
    , link: function (scope, element, attr) {
      if (scope.ngModel) {
        scope.selectAccount(scope.ngModel);
      }
    }
    , controller: "AccountController"
  };
});
