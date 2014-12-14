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
  if ($scope.directive == undefined) {
    $scope.directive = "AccountController";
  }

  $scope.title = "Account";
  $scope.name = "account_id";
  $scope.editing = false;
  var rollback = null;

  $scope.getId = function()
  {
    return $scope.account_id;
  };

  $scope.cancel = function ()
  {
    $scope.collectionSelection.rollback();
    $scope.editing = false;
  };

  $scope.edit = function ()
  {
    $scope.collectionSelection.beginEditing();
    $scope.editing = true;
  };

  $scope.new = function()
  {
    $scope.collectionSelection.beginEditingNewItem(new AccountsApi());
    $scope.editing=true;
  };

  $scope.save = function ()
  {
    $scope.collectionSelection.saveItem();
  };

  $scope.update = function ()
  {
    $scope.collectionSelection.saveItem();
  };

  if ($transclude != undefined) {
    $transclude(function (clone, scope)
    {
      $scope.title = clone.html();
      if ($scope.title == undefined)  $scope.title = "Account";
    });
  }

  $scope.selectAccount = function(accountId)
  {
    return $scope.collectionSelection.selectItem(accountId);
  }
});

feenance.directive('accountSelector', function() {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      selected: "=ngModel",
      accountId: "=", // remember account_id in markup accountId in directive / controller
      name: "@"
    },
    templateUrl: '/view/account_selector.html',
    link: function (scope, element, attr)
    {
      scope.directive = "accountSelector";
      if (scope.accountId) {
        scope.selectAccount(scope.accountId);
      }
    },
    controller: "AccountController"
  };
});

feenance.directive('accountIdSelector', function() {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      account_id: "=ngModel",
      ngModel: "=",
      name: "@"
    },
    templateUrl: '/view/account_selector.html',
    link: function (scope, element, attr) {
      scope.directive = "accountIdSelector";
      if (scope.account_id > 0) {
        scope.selectAccount(scope.account_id);
      }
      else if (attr.accountId) {
        scope.selectAccount(attr.accountId);
      }
    },
    controller: "AccountController"
  };
});

feenance.directive('accountName', function() {
  return {
    restrict: 'E',
    scope: {
      account_id: "=ngModel",
      ngModel: "="
    },
    template: '{{selected.name}}',
    link: function (scope, element, attr)
    {
      scope.directive = "accountName";
      if (scope.ngModel) {
        scope.selectAccount(scope.ngModel);
      }
    },
    controller: "AccountController"
  };
});
