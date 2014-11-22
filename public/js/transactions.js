feenance.controller('TransactionsController', function($scope, TransactionsApi, AccountsApi) {
  $scope.account_id = 2;
  $scope.transactions = null;
  $scope.predicate    = ["date", "id"];
  $scope.reverse      = true;
  $scope.reconciled_all     = true;
  $scope.reconciled_only    = false;
  $scope.unreconciled_only  = false;

  $scope.showReconciled = function(val) {
    $scope.reconciled_all     = (val == undefined);
    $scope.reconciled_only    = !$scope.reconciled_all && (val == "reconciled");
    $scope.unreconciled_only  = !$scope.reconciled_all && (val == "unreconciled");
  }

  $scope.reconciledFilter = function(element) {
    return  $scope.reconciled_only   ? element.reconciled >  0 ?  true : false
      :     $scope.unreconciled_only ? element.reconciled <= 0 ?  true : false
      : true;
  };

  $scope.$on('addTransactions', function($event, $transactions) {
    angular.forEach($transactions, function($transaction, $key) {
      if ($transaction.account_id == $scope.account_id) {
        $scope.transactions.push($transaction);
      }
    });
  });

  $scope.onClickBankString = function(bank_string_id) {
//    alert(bank_string_id);
    $scope.$emit("BankStringClicked", bank_string_id);
  };


  $scope.deleteTransaction = function(transaction, $index) {

    TransactionsApi.delete({ id: transaction.id}, function(response) {

      alert("Transaction deleted.");
      $scope.transactions.splice($index, 1);

    }, function() {
      alert("Failed to delete the transaction.");
    });

  };

  $scope.selectTransaction = function($transaction) {
    if ($scope.editingTransaction != undefined) {
      $scope.editingTransaction.edit = false;
    }
    $transaction.edit = true;
    $scope.editingTransaction = $transaction;

    $scope.$emit("selectTransaction", $transaction);
  };

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

  $scope.refresh = function () {
    $scope.onSetAccount($scope.account_id);
  };

  $scope.deleteUnreconciled = function() {
    if ($scope.account_id) {
      alert("This will delete all unreconciled transactions for account " + $scope.account_id);
      AccountsApi.deleteUnreconciled({id:$scope.account_id}, function(response) {
        alert("Successfully deleted all unreconciled transactions");
      }, function() {
        alert("Deletion failed");
      });
    } else {
      alert("No account set for bulk delete ");
    }
  };

  $scope.reconcileAll = function() {
    if ($scope.account_id) {
      alert("This will reconciled transactions for account " + $scope.account_id);
      AccountsApi.reconcileAll({id:$scope.account_id}, function(response) {
        alert("Successfully reconciled all transactions");
      }, function() {
        alert("Failed to reconcile all transactions");
      });
    } else {
      alert("No account set for bulk update ");
    }
  };


  $scope.onSetAccount = function ($newAccount_id) {
    if ($newAccount_id) {
      $scope.account_id = $newAccount_id;
      var accountsApiParameters =  {id:$newAccount_id, page:$scope.page  };
      if ($scope.reconciled_only) {
        accountsApiParameters.filter = "reconciled";
      } else if ($scope.unreconciled_only) {
        accountsApiParameters.filter = "unreconciled";
      }
      var records = AccountsApi.transactions( accountsApiParameters ,function () {
        $scope.transactions = records.data;
//        $scope.accountId = $newAccount_id;
        $scope.paginator = records.paginator;
      });
    }
  };

  $scope.$watch('account_id',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        $scope.onSetAccount($scope.account_id);
      }
    }
  );
});