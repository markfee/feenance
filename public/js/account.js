feenance.controller('AccountController', function($scope, $transclude, AccountsApi) {
  $scope.accounts   = {};
  $scope.selected   = null;
  $scope.selectedId = null;
  $scope.title = "Account";
  $scope.name = "account_id";
  $scope.emitMessage = "accountUpdated";
  $scope.optional = false;

  var records = AccountsApi.get( {}, function () {
    $scope.accounts = records.data;
    if ($scope.optional) {
      $scope.accounts.splice(0, 0, { "id":null, name:""});
      $scope.selected = records.data[0];
    }
    if ($scope.selectedId) {
      $scope.select($scope.selectedId);
    }
  });


  $transclude(function(clone,scope) {
    $scope.title = clone.html();
    if ($scope.title == undefined)  $scope.title = "Account";
  });

  $scope.$on('setAccount', function (something, $newAccount) {
    if ($scope.linkedAccount){
      $scope.select($newAccount.id);
    }
  });

  $scope.select = function(id) {
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
    $scope.$emit($scope.emitMessage, $scope.selected);
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
 , templateUrl: 'view/accountSelector.html'
    , link: function (scope, element, attr) {
      scope.emitMessage =  attr.emitMessage ? attr.emitMessage : scope.emitMessage;
      scope.linkedAccount = attr.linkedAccount ? true : false;
      scope.optional = attr.optional ? true : false;
      if (scope.accountId) {
        scope.select(scope.accountId);
      }
    }
    , controller: "AccountController"
  };
});