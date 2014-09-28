feenance.controller('StandingOrderController', function($scope, StandingOrdersApi) {
  $scope.standingOrders = null;
  $scope.predicate    = ["next_date"];
  $scope.reverse      = false;

  var standingOrders = StandingOrdersApi.get({},
    function() {
      $scope.standingOrders = standingOrders.data;
    }
  );
});

feenance.directive('standingOrdersTable', function() {
  return {
    restrict: 'E',
    scope: {    },
    templateUrl: 'view/standingOrdersTable.html'
    , link: function (scope) {
    }
    , controller: "StandingOrderController"
  };
});

