feenance.controller('TransactionController', function($scope, TransactionsApi) {
  // Set the default for the Form!
  $scope.page = 1;
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
    $scope.page = 1;
  };
  $scope.success        = null;
  $scope.reset();

  var __setTransaction = function(transaction_id) {
    var transaction = TransactionsApi.get({id:transaction_id}, function() {
      $scope.transaction = transaction;
      $scope.transaction.account      = null;
      $scope.transaction.transfer     = null;
      $scope.transaction.payee        = null;

      $scope.transaction.date         =  transaction.date.substr(0,10);
      if (transaction.account_id) {
        $scope.$broadcast('setAccount', transaction.account_id);
      }
      if (transaction.category_id)
        $scope.$broadcast('setCategory', transaction.category_id);
      if (transaction.payee_id)
        $scope.$broadcast('setPayee', transaction.payee_id);
    });
  };

  $scope.setTransaction = function(transaction_id) {
    __setTransaction(transaction_id);
  //  if (!$scope.transaction.account)
      __setTransaction(transaction_id);
  }


  $scope.$on('editTransaction', function (event, transaction) {
    $scope.setTransaction(transaction.id);
  });

  $scope.$on('payeeUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
    $scope.transaction.payee_id = (item.id) ? item.id : null;
  });

  $scope.$on('categoryUpdated', function (something, item) {
    $scope.transaction.category_id = (item.id) ? item.id : null;
  });

  $scope.$on('updatedAccount', function ($event, item) {
    $event.stopPropagation();
    console.log("updatedAccount in TransactionController" + item.id);
    $scope.transaction.account_id = (item.id) ? item.id : null;
  });

  $scope.$on('updatedTransfer', function ($event, item) {
    $event.stopPropagation();
    console.log("updatedTransfer in TransactionController" + item.id);
    $scope.transaction.transfer_id = (item.id) ? item.id : null;
  });

  $scope.add = function(transaction) {
    if ($scope.transaction.id != undefined) {
      alert("Attempt to Save Existing Transaction");
      return;
    }
    $scope.transaction.$save( function(response) {
        $scope.success = "Saved Successfully";
        // Make sure that an array of refreshTransactions is emitted - even if it's just one.
        $transactions = (response.data ? response.data :  [response]);
        $scope.$emit("refreshTransactions", $transactions);
        $scope.setTransaction($transactions[0].id);
      } , function(response) {
        $scope.success = response.data.errors.error[0];
      }
    );
  }

  $scope.update = function(transaction) {
    if ($scope.transaction.id == undefined) {
      alert("Attempt to update new Transaction");
      return;
    }
    $scope.transaction.$update( function(response) {
        $scope.success = "Saved Successfully";
        $transactions = (response.data ? response.data :  [response]);
        $scope.$emit("refreshTransactions", $transactions);
        $scope.setTransaction($transactions[0].id);
      } , function(response) {
        $scope.success = response.data.errors.error[0];
      }
    );
  }


});

feenance.controller('TransactionsController', function($scope, TransactionsApi, AccountsApi) {
  $scope.transactions = null;
  $scope.predicate    = ["date", "id"];
  $scope.reverse      = true;

  $scope.$on('addTransactions', function($event, $transactions) {
    angular.forEach($transactions, function($transaction, $key) {
      if ($transaction.account_id == $scope.accountId) {
        $scope.transactions.push($transaction);
      }
    });
  });

  $scope.onClickBankString = function(bank_string_id) {
//    alert(bank_string_id);
    $scope.$emit("BankStringClicked", bank_string_id);
  };


  $scope.selectTransaction = function($transaction) {
    if ($scope.editingTransaction != undefined) {
      $scope.editingTransaction.edit = false;
    }
    $transaction.edit = true;
    $scope.editingTransaction = $transaction;

    $scope.$emit("selectTransaction", $transaction);
  }

  $scope.toggleReconciled = function(transaction) {
    var reconciled = transaction.reconciled;
    transaction.reconciled = reconciled > 0 ? 0 : 1;
    TransactionsApi.update({ id: transaction.id}, transaction, function(response) {
      transaction.reconciled = response.reconciled;
    }, function() {
      // On Error reset Value
      transaction.reconciled = reconciled;
    });

  };

  $scope.newPage = function () {
    $scope.onSetAccount($scope.account);
  };

  $scope.onSetAccount = function ($newAccount) {
    if ($newAccount.id) {
      $scope.account = $newAccount;
      var records = AccountsApi.transactions( {id:$newAccount.id, page:$scope.page  },function () {
        $scope.transactions = records.data;
        $scope.accountId = $newAccount.id;
        $scope.paginator = records.paginator;
      });
    }
  }

  $scope.$on('setAccount', function ($event, $newAccount) {
    $scope.onSetAccount($newAccount);
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

feenance.directive('transactionUploader', function() {
  return {
    restrict: 'E',
    scope: {
      uploadFile: "=ngModel"
    },
    templateUrl: 'view/transactionUploader.html'
    , link: function (scope) {   }
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
