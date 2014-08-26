feenance.controller('AccountsController', function($scope, AccountsApi, CurrentAccount) {
  var records = AccountsApi.get( {  },
    function () {
      $scope.accounts = records.data;
      $scope.myAccount = records.data[0];
      CurrentAccount.set($scope.myAccount);
    });
  $scope.change = function() {
    CurrentAccount.set($scope.myAccount);
  }
});

feenance.controller('AccountController', function($scope, AccountsApi) {
  var records = AccountsApi.get( {  },
    function () {
      $scope.accounts = records.data;
      $scope.myAccount = records.data[0];
      CurrentAccount.set($scope.myAccount);
    });
  $scope.change = function() {
    CurrentAccount.set($scope.myAccount);
  }
});

feenance.directive('Account', function() {
  return {
      templateUrl: "Account.html"
  };
});



feenance.controller('TransactionsController', function($scope, AccountsApi, CurrentAccount) {
  CurrentAccount.onChange(function($newAccount) {
    if ($newAccount.id) {
      var records = AccountsApi.transactions( {id:$newAccount.id  },function () {
        $scope.items = records.data;
        $scope.accountId = $newAccount.id;
      });
    }
  });
});


feenance.directive('account', function(AccountsApi) {
    return {
      restrict: 'E',
      scope: {
        accountid: "="
      },
      templateUrl: 'account.html'
      , link: function (scope, element, attrs) {
        if (scope.accountid)
        {
          var $account = AccountsApi.get({id:scope.accountid}, function() {
            scope.account = $account;
            scope.direction = attrs.direction
          });
        }
      }
    };
  });
