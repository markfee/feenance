var global_count = 0; //Just an easy way to identify the instances of the controllers when debugging.

feenance.factory('AccountCollection', function(AccountsApi, Collection) {
  return (new Collection(AccountsApi, "<Please select an account>")).fetchAll();
});

feenance.controller('AccountController', function($scope, $transclude, AccountCollection, CollectionSelection) {
  var collectionSelection = new CollectionSelection(AccountCollection, $scope, "account_id");
  $scope.predicate = ["-open", "name"];

  if ($scope.directive == undefined) {
    $scope.directive = "AccountController_" + global_count++;
  }

  $scope.title = "Account";
  $scope.name = "account_id";

  if ($transclude != undefined) {
    $transclude(function (clone, scope)
    {
      $scope.title = clone.html();
      if ($scope.title == undefined)  $scope.title = "Account";
    });
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
      scope.directive = "accountSelector_"  + global_count++;
      if (scope.accountId) {
        scope.selectItem(scope.accountId);
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
      scope.directive = "accountIdSelector_"  + global_count++;
      if (scope.account_id > 0) {
        scope.selectItem(scope.account_id);
      }
      else if (attr.accountId) {
        scope.selectItem(attr.accountId);
      }
    },
    controller: "AccountController"
  };
});

feenance.directive('accountName', function() {
  return {
    restrict: 'E',
    scope: {
      ngModel: "="
    },
    template: '{{selected.name}}',
    link: function (scope, element, attr)
    {
      scope.directive = "accountName_"  + global_count++;
      if (scope.ngModel) {
        scope.selectItem(scope.ngModel);
      }
    },
    controller: "AccountController"
  };
});
