feenance.factory('AccountCollection', function(Notifier, AccountsApi, $filter) {
  var collection = { data: [] };
  var $_PLEASE_SELECT =  {id: null, name: "<Please select an account>"};
  collection.data[0] = $_PLEASE_SELECT;

  var accounts = AccountsApi.get({},
    function()
    {
      angular.extend(collection.data, accounts.data);
      collection.data.splice(0, 0, $_PLEASE_SELECT);
    }
  );

  return {
    collection:
      function ()
      {
        return collection.data;
      },
    get:
      function (id)
      {
        var account = null;
        angular.forEach(collection.data,
          function(value)
          {
            if (value.id == id) {
              account = value;
            }
          }
        );
        return account;
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
  $scope.selectedId = null;
  $scope.initialSelect = null;
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

  $transclude(function(clone,scope) {
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

  $scope.change = function() {
    $scope.selectedId = $scope.selected.id;
    var message = "updated"+$scope.emitMessage;
    console.log("emitting: " +  message + " from " + $scope.title);

    $scope.$emit(message, $scope.selected);
  };
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
        scope.initialSelect = scope.accountId;
//        scope.select(scope.accountId);
      }
    }
    , controller: "AccountController"
  };
});