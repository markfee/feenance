feenance.controller('AccountController', function($scope, $transclude, AccountsApi) {
  $scope.accounts   = {};
  $scope.selected   = null;
  $scope.selectedId = null;
  $scope.initialSelect = null;
  $scope.title = "Account";
  $scope.name = "account_id";
  $scope.emitMessage = "Account";
  $scope.optional = false;
  $scope.editing = false;

  var records = AccountsApi.get( {}, function () {
    $scope.accounts = records.data;
    if ($scope.optional) {
      $scope.accounts.splice(0, 0, { "id":null, name:""});
      $scope.selected = records.data[0];
    }
    if ($scope.initialSelect) {
      $scope.select($scope.initialSelect);
    }
  });

  $scope.cancel = function () {
    $scope.select($scope.selected.id);
  };

  $scope.edit = function () {
    var editRecord = AccountsApi.get({id:$scope.selected.id}, function() {
      $scope.selected = editRecord;
      $scope.editing = true;
      $scope.selectedId = $scope.selected.id;
    });
  };



  $scope.new = function() {
    $scope.selected = new AccountsApi();
    $scope.editing=true;
  };

  $scope.save = function () {
    $scope.selected.$save(function(response) {
      $scope.selected = response;
      alert(response.id);
      $scope.select(response.id);
    });
  };

  $scope.update = function () {
    AccountsApi.update({id:$scope.selected.id}, $scope.selected, function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  };



  $transclude(function(clone,scope) {
    $scope.title = clone.html();
    if ($scope.title == undefined)  $scope.title = "Account";
  });

  $scope.$on('setAccount', function (event, $newAccount) {
    if ($scope.emitMessage != "Account") {
      return;
    }
    if ($newAccount.id == undefined) {
      console.log("received: setAccount "+$newAccount+" in " + $scope.title);
      $scope.select($newAccount);
    }
    else {
      console.log("received: setAccount "+$newAccount.id+" in " + $scope.title);
      $scope.select($newAccount.id);
    }
  });

  $scope.$on('setTransfer', function (event, $newAccount) {
    if ($scope.emitMessage != "Transfer") {
      return;
    }
    console.log("received: setTransfer in " + $scope.title);
    if ($newAccount.id == undefined) {
      $scope.select($newAccount);
    }
    else {
      $scope.select($newAccount.id);
    }
  });



  $scope.select = function(id) {
    $scope.editing = false;
    var $changed = ($scope.selectedId != id);
    $scope.selectedId = id;
    angular.forEach($scope.accounts, function(account, key) {
      if (account.id == id) {
        $scope.selected =   records.data[key];
        if ($changed) {
          $scope.change();
        }
        return;
      }
    });
  };

  $scope.change = function() {
    $scope.selectedId = $scope.selected.id;
    var message = "updated"+$scope.emitMessage;
    console.log("emitting: " +  message + " from " + $scope.title);

    $scope.$emit(message, $scope.selected);
  };
});

feenance.directive('accountSelector', function() {
 return {
  restrict: 'E'
 , transclude: true
 ,  scope: {
      selected: "=ngModel"
     , accountId: "=" // remember account_id in markup accountId in directive / controller
     , name: "@"
    }
 , templateUrl: '/view/accountSelector.html'
    , link: function (scope, element, attr) {
     scope.emitMessage =  attr.emitMessage ? attr.emitMessage : scope.emitMessage;
      scope.optional = attr.optional ? true : false;
      if (scope.accountId) {
        scope.initialSelect = scope.accountId;
//        scope.select(scope.accountId);
      }
    }
    , controller: "AccountController"
  };
});