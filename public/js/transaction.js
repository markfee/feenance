feenance.controller('TransactionsController', function($scope, TransactionsApi, AccountsApi, CurrentAccount) {
  // Set the default for the Form!
  $scope.transaction = {
    "reconciled": "true",
    "date": (new Date()).toISOString().substr(0,10),
    "amount": 0.0
  };

  $scope.$on('payeeUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
  });

  CurrentAccount.onChange(function($newAccount) {
    if ($newAccount.id) {
      $scope.account = $newAccount;
      var records = AccountsApi.transactions( {id:$newAccount.id  },function () {
        $scope.transactions = records.data;
        $scope.accountId = $newAccount.id;
      });
    }
  });

  $scope.update = function(transaction) {
//    TransactionsApi
    alert(transaction.amount);
  }
});

feenance.directive('newTransaction', function(AccountsApi) {
  return {
    restrict: 'E',
    scope: {
//      accountId: "=" // remember account_id in markup accountId in directive / controller ???
    },
    templateUrl: 'view/newTransaction.html'
    , link: function (scope) {
      if (scope.accountId) {
        var $account = AccountsApi.get({id:scope.accountId}, function() {
          scope.account= $account;
        });
      }
    }
    , controller: "TransactionsController"
  };
});

feenance.directive('transfer', function(TransactionsApi, AccountsApi) {
  return {
    restrict: 'E',
    scope: {
        source: "="
      , destination: "="
    },
    templateUrl: 'view/transfer.html'
    , link: function (scope) {
      if (scope.source || scope.destination) {
        var $id = (scope.source == undefined ? scope.destination : scope.source);
        var $direction = (scope.source == undefined ? "to" : "from");
        var $transaction = TransactionsApi.get({id:$id}, function() {
          scope.transaction = $transaction;
          var $account = AccountsApi.get({id:$transaction.account_id}, function() {
            scope.account = $account;
            scope.direction = $direction;
          });
        });
      }
    }
  };
});
