feenance.factory('StandingOrderCollection', function(Notifier, StandingOrdersApi, Paginator, $filter) {
  var collection = {data: []};
  collection.data[0] = {id: null, name: "<Please Select>"};
  var standingOrders = StandingOrdersApi.get({},
    function()
    {
      angular.extend(collection.data, standingOrders.data);
      collection.data.splice(0, 0, {id: null, name: "<Please Select>"});
      angular.forEach(collection.data,
        function(value)
        {
          transform(value);
        }
      );
    }
  );

  function transform(standingOrder)
  {
    try {
      if (standingOrder.previous_date)
        standingOrder.previous_date = $filter("isoDate")(standingOrder.previous_date);
      if (standingOrder.next_date)
        standingOrder.next_date     = $filter("isoDate")(standingOrder.next_date);
    } catch(exception) {
    }
  }

  return {
    collection:
      function ()
      {
        return collection.data;
      }
  }

});

feenance.controller('StandingOrderController', function($scope, StandingOrdersApi, StandingOrderCollection) {
  $scope.standingOrders = StandingOrderCollection.collection();
  $scope.selected       = $scope.standingOrders[0]; // the currently selected standing order.
  $scope.rollback       = null; // used to rollback edits
  $scope.predicate    = ["next_date"];
  $scope.reverse      = false;
  $scope.optional     = false;

  $scope.increment = function($id, $index) {
    StandingOrdersApi.increment({ id: $id}, function(response) {
      $scope.standingOrders[$index] = response;
    });
  }

  $scope.sort = function(predicate) {
    if ($scope.predicate == predicate) {
      $scope.reverse=!$scope.reverse;
    } else {
      $scope.predicate = predicate;
      $scope.reverse=false;
    }
  };

  $scope.edit = function(transaction) {
    $scope.selected = transaction;
    $scope.rollback = angular.copy($scope.selected);
  }

  $scope.cancel = function(transaction) {
    angular.extend($scope.selected, $scope.rollback);
    $scope.rollback = null;
    $scope.selected = null;
  }

  $scope.change = function() {
    $scope.selectedId = $scope.selected.id;
    var message = "standingOrderUpdated";
    $scope.$emit(message, $scope.selected);
  };

});

feenance.directive('standingOrdersTable', function() {
  return {
    restrict: 'E',
    scope: {    },
    templateUrl: '/view/standing_order_table.html'
    , link: function (scope) {
    }
    , controller: "StandingOrderController"
  };
});

feenance.directive('standingOrderForm', function() {
  return {
    restrict: 'E',
    scope: {
      selected: "=ngModel"
    },
    templateUrl: '/view/standing_order_form.html',
    link: function (scope) {
    }
  };
});

feenance.directive('standingOrderSelector', function() {
  return {
    restrict: 'E',
    scope: {
      selected: "=ngModel",
      name: "@"
    },
    templateUrl: '/view/standing_order_selector.html',
    link: function (scope, attributes) {
      scope.optional     = true;
      scope.predicate = "name";
      if (scope.name == undefined) {
        scope.name = "standing_order_id"; // TODO - this doesn't work for default values
      }
    }
    , controller: "StandingOrderController"
  };
});