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

feenance.controller('TransactionsController', function($scope, TransactionsApi, AccountsApi, CurrentAccount) {
  // Set the default for the Form!
  $scope.transaction = {
    "reconciled": "true",
    "date": (new Date()).toISOString().substr(0,10),
    "amount": 0.0
  };
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

feenance.controller('PayeeController', function($scope, $http, PayeesApi) {
  // Set the default for the Form!
  $scope.selected = undefined;
  $scope.transaction = {
    "reconciled": "true",
    "date": (new Date()).toISOString().substr(0,10),
    "amount": 0.0
  };

  $scope.lookupResults = [];
  $scope.lookupPayees = function($viewValue, $page, $mid) {
    if ($page == undefined) {
      $scope.lookupResults = [];
    }
    if ($page == undefined) {
      $page = 1;
    }
    $options = "?page="+$page;
    if ($mid != undefined) {
      $options = $options+"&mid";
    }
    return $http.get($API_ROOT + "payees/"+$viewValue + $options).then(function(response) {
      var $payees = response.data;
      $scope.lookupResults = $scope.lookupResults.concat($payees.data);
      if ($payees.paginator.next != undefined) {
        $scope.lookupPayees($viewValue, $payees.paginator.next, $mid);
      } else if ($mid == undefined) {
        $scope.lookupPayees($viewValue, 1, true);
      }
      return $scope.lookupResults;
    });
  };

  function getPage($page) {
    $payees = PayeesApi.get({page: $page}, function() {
      $scope.payees = $scope.payees.concat($payees.data);
      if ($payees.paginator.next != undefined) {
        getPage($payees.paginator.next);
      }
    });
  }

  $scope.payees = [];
  getPage(1);
  $scope.update = function(payee) {
    alert(payee.name);
  }
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

feenance.directive('payee', function(PayeesApi) {
  return {
    restrict: 'E',
    scope: {
      payeeid: "="
    },
    templateUrl: 'payee.html'
    , link: function (scope) {
      if (scope.payeeid) {
        var $payee = PayeesApi.get({id:scope.payeeid}, function() {
          scope.payee= $payee;
        });
      }
    }
  };
});

feenance.directive('transaction', function(TransactionsApi, AccountsApi) {
  return {
    restrict: 'E',
    scope: {
        source: "="
      , destination: "="
    },
    templateUrl: 'transaction.html'
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
