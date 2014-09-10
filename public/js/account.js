feenance.controller('AccountController', function($scope, AccountsApi) {
  $scope.accounts   = {};
  $scope.selected   = null;
  $scope.selectedId = null;

  var records = AccountsApi.get( {}, function () {
    $scope.accounts = records.data;
    if ($scope.selectedId) {
      $scope.select($scope.selectedId);
    }
  });

  $scope.select= function(id) {
    $scope.selectedId = id;
    angular.forEach($scope.accounts, function(account, key) {
      if (account.id == id) {
        $scope.selected =   records.data[key];
        $scope.change();
        return;
      }
    });
  };

  $scope.change = function() {
    $scope.selectedId = $scope.selected.id;
    $scope.$emit('accountUpdated', $scope.selected);
  };
});

feenance.directive('accountSelector', function() {
 return {
    restrict: 'E'
 ,  scope: {
      selected: "=ngModel"
      , accountId: "=" // remember account_id in markup accountId in directive / controller ???
    }
 , templateUrl: 'view/accountSelector.html'
    , link: function (scope, element, attr) {
        if (scope.accountId) {
          scope.select(scope.accountId);
        }
    }
    , controller: "AccountController"
  };
});


feenance.directive('account', function(AccountsApi) {
  return {
    restrict: 'E',
    scope: {
      accountid: "="
    },
    templateUrl: 'account.html'
    , link: function (scope, element, attrs) {
      if (scope.accountid) {
        var $account = AccountsApi.get({id:scope.accountid}, function() {
          scope.account = $account;
          scope.direction = attrs.direction
        });
      }
    }
  };
});
