feenance.controller('MapController', function($scope, BankStringsApi) {

  $scope.reset = function() {
    $scope.map.bank_string_id   = null;
    $scope.map.bank_string      = null;
    $scope.map.category_id      = null;
    $scope.map.account_id       = null;
    $scope.map.transfer_id      = null;
    $scope.map.payee_id         = null;
    $scope.map.account          = null;
    $scope.map.transfer         = null;
    $scope.map.payee            = null;
  }

  $scope.$on('editMap', function($event, bank_string_id) {
    $scope.reset();
     var map = new BankStringsApi.transactions({id:bank_string_id}, function() {
       $scope.map = map.data[0];

       if ($scope.map.category_id)
         $scope.$broadcast('setCategory', $scope.map.category_id);
       if ($scope.map.payee_id)
         $scope.$broadcast('setPayee', $scope.map.payee_id);
    });
  });
  $scope.success        = null;


  $scope.$on('updatedAccount', function ($event, item) {
    $event.stopPropagation();
    console.log("updatedAccount in MapController" + item.id);
    $scope.map.account_id = (item.id) ? item.id : null;
  });

  $scope.$on('updatedTransfer', function ($event, item) {
    $event.stopPropagation();
    console.log("updatedAccount in MapController" + item.id);
    $scope.map.transfer_id = (item.id) ? item.id : null;
  });

  $scope.$on('payeeUpdated', function (something, item) {
    $scope.$broadcast('setCategory', item.category_id);
    $scope.map.payee_id = (item.id) ? item.id : null;
  });

  $scope.$on('categoryUpdated', function (something, item) {
    $scope.map.category_id = (item.id) ? item.id : null;
  });

  $scope.mapUpdate = function(bank_string_id) {

    BankStringsApi.map({id:bank_string_id}, $scope.map, function(response) {
      alert(response.data + " records updated");
      $scope.$emit("refreshTransactions");
      $scope.reset();
    });
  }


});

feenance.directive('newMap', function() {
  return {
    restrict: 'E',
    scope: {    },
    templateUrl: 'view/newMap.html'
    , link: function (scope) {

    }
    , controller: "MapController"
  };
});