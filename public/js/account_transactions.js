feenance.directive('reconciledFilter', function() {
  return {
    restrict: 'E',
    scope: {
      reconciled_filter: "=ngModel"
    },
    templateUrl: '/view/reconciled_filter.html'
    , link: function (scope) {
      var my_filter = {
        reconciled_all:     true,
        reconciled_only:    false,
        unreconciled_only:  false
      };

      scope.showReconciled = function(val) {
        my_filter.reconciled_all     = (val == undefined);
        my_filter.reconciled_only    = !my_filter.reconciled_all && (val == "reconciled");
        my_filter.unreconciled_only  = !my_filter.reconciled_all && (val == "unreconciled");
      };

      scope.reconciled_filter = function(element) {
        return  my_filter.reconciled_only   ? element.reconciled >  0 ?  true : false
          :     my_filter.unreconciled_only ? element.reconciled <= 0 ?  true : false
          : true;
      };
      scope.my_filter = my_filter;
    }
  };
});

feenance.controller('AccountTransactionsController', function($scope, TransactionsApi, AccountsApi, $timeout) {

  $scope.account_id = -1;
  $scope.transactions = null;
  $scope.predicate    = ["date", "id"];
  $scope.reverse      = true;

  $scope.$on('addTransactions', function($event, $transactions) {
    angular.forEach($transactions, function($transaction, $key) {
      if ($transaction.account_id == $scope.account_id) {
        $scope.transactions.push($transaction);
      }
    });
  });

  $scope.onClickBankString = function(bank_string_id) {
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

  __SetAccount = function ($newAccount_id) {
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
        $scope.account_id = $newAccount_id;
        $scope.paginator = records.paginator;
      });
    }
  };

  $scope.refresh = function () {
    __SetAccount($scope.account_id);
  };

  $scope.$watch('account_id',
    function(new_val, old_val) {
      if (new_val && new_val != old_val) {
        __SetAccount($scope.account_id);
      }
    }
  );
});

