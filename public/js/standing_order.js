feenance.controller('StandingOrderController', function($scope, StandingOrdersApi) {
  $scope.standingOrders = null;
  $scope.predicate    = ["next_date"];
  $scope.reverse      = false;
  $scope.optional     = false;

  var standingOrders = StandingOrdersApi.get({},
    function() {
      $scope.standingOrders = standingOrders.data;
      if ($scope.optional) {
        $scope.standingOrders.splice(0, 0, { "id":null, name:""});
        $scope.selected = $scope.standingOrders[0];
      }
    }
  );
  $scope.sort = function(predicate) {
    if ($scope.predicate == predicate) {
      $scope.reverse=!$scope.reverse;
    } else {
      $scope.predicate = predicate;
      $scope.reverse=false;
    }
  };

  $scope.change = function() {
    $scope.selectedId = $scope.selected.id;
    var message = "standingOrderUpdated";
    console.log("emitting: standingOrderUpdated from standingOrderSelector");

    $scope.$emit(message, $scope.selected);
  };

});

feenance.directive('standingOrdersTable', function() {
  return {
    restrict: 'E',
    scope: {    },
    templateUrl: '/view/standingOrdersTable.html'
    , link: function (scope) {
    }
    , controller: "StandingOrderController"
  };
});

feenance.directive('standingOrderSelector', function() {
  return {
    restrict: 'E',
    scope: {
      selected: "=ngModel"
    },
    templateUrl: '/view/standingOrderSelector.html'
    , link: function (scope) {
      scope.optional     = true;
      scope.predicate = "name";
    }
    , controller: "StandingOrderController"
  };
});