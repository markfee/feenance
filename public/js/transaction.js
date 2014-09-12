feenance.controller('TransactionController', function($scope, TransactionsApi, AccountsApi, CurrentAccount) {
  // Set the default for the Form!
  $scope.transaction = new TransactionsApi();
  $scope.transaction.reconciled   = true;
  $scope.transaction.date         =  (new Date()).toISOString().substr(0,10);
  $scope.transaction.amount       = 0.0;
  $scope.transaction.category_id  = null;
  $scope.transaction.account_id   = null;
  $scope.transaction.transfer_id  = null;
  $scope.transaction.payee_id     = null;
  $scope.transaction.account      = null;
  $scope.transaction.transfer     = null;
  $scope.transaction.payee        = null;

  $scope.$on('payeeUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
    $scope.transaction.payee_id = (item.id) ? item.id : null;
  });

  $scope.$on('categoryUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
    $scope.transaction.category_id = (item.id) ? item.id : null;
  });

  $scope.$on('accountUpdated', function ($event, item) {
    $event.stopPropagation();
    console.log("accountUpdated in TransactionController" + item.id);
    $scope.transaction.account_id = (item.id) ? item.id : null;
  });

  $scope.$on('transferUpdated', function ($event, item) {
    $event.stopPropagation();
    console.log("accountUpdated in TransactionController" + item.id);
    $scope.transaction.transfer_id = (item.id) ? item.id : null;
  });

  $scope.$on('setAccount', function (something, $newAccount) {
    if ($newAccount.id) {
      $scope.account = $newAccount;
      var records = AccountsApi.transactions( {id:$newAccount.id  },function () {
        $scope.transactions = records.data;
        $scope.accountId = $newAccount.id;
      });
    }
  });

  $scope.add = function(transaction) {
    $scope.transaction.$save();
//    alert(transaction.amount);
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
    , controller: "TransactionController"
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
