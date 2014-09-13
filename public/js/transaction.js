feenance.controller('TransactionController', function($scope, TransactionsApi, AccountsApi) {
  // Set the default for the Form!
  $scope.reset = function() {
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
  };
  $scope.success        = null;
  $scope.reset();

  $scope.$on('payeeUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
    $scope.transaction.payee_id = (item.id) ? item.id : null;
  });

  $scope.$on('categoryUpdated', function (something, item) {
//    $scope.$broadcast('setCategory', item.category_id);
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

  $scope.add = function(transaction) {
    $scope.transaction.$save( function(response) {
        $scope.success = "Saved Successfully";
        // Make sure that an array of newTransactions is emitted - even if it's just one.
        $transactions = (response.data ? response.data :  [response]);
        $scope.$emit("newTransactions", $transactions);
        $scope.reset();
    } , function(response) {
        $scope.success = response.data.errors.error[0];
      }
    );
  }
});

feenance.controller('TransactionsController', function($scope, TransactionsApi, AccountsApi) {
  $scope.transactions = null;
  $scope.predicate    = "date";
  $scope.reverse      = true;

  $scope.$on('addTransactions', function($event, $transactions) {
    angular.forEach($transactions, function($transaction, $key) {
      if ($transaction.account_id == $scope.accountId) {
        $scope.transactions.push($transaction);
      }
    });
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
});


feenance.directive('newTransaction', function(AccountsApi) {
  return {
    restrict: 'E',
    scope: {    },
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
