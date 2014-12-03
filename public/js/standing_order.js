feenance.factory('StandingOrderCollection', function(Notifier, StandingOrdersApi, Paginator, $filter) {
  var collection = { data: [] };
  var $_PLEASE_SELECT =  {id: null, name: "<Please Select>"};
  collection.data[0] = $_PLEASE_SELECT;

  var standingOrders = StandingOrdersApi.get({},
    function()
    {
      angular.extend(collection.data, standingOrders.data);
      collection.data.splice(0, 0, $_PLEASE_SELECT);
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

feenance.controller('StandingOrderController', function($scope, StandingOrdersApi, StandingOrderCollection, AccountCollection) {
  $scope.standingOrders = StandingOrderCollection.collection();
  $scope.selected = new StandingOrdersApi();
  var rollback       = null; // used to rollback edits
  $scope.predicate    = ["next_date"];
  $scope.reverse      = false;

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

  $scope.$watch('selected.account_id',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        $scope.selected.account = AccountCollection.getPromisedIndex($scope.selected.account_id);
      }
    }
  );

  $scope.$watch('selected.account.id',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        $scope.selected.account_id = $scope.selected.account.id;
      }
    }
  );


  $scope.edit = function(transaction) {
    $scope.selected = transaction;
    rollback = angular.copy($scope.selected);
  }

  $scope.cancel = function(transaction) {
    angular.extend($scope.selected, rollback);
    rollback = null;
    $scope.selected = null;
    $scope.selected = new StandingOrdersApi();
  }

  function newRecord() {
    try {
      if ($scope.selected.id != undefined && $scope.selected.id > 0) {
        return false;
      }
    } catch(e) {

    }
    return true;
  }

  $scope.save = function() {
    if (newRecord()) {
      $scope.selected.$save(
        function(response)
        {
          alert("Saved New Record Successfully");
        }
      );
    } else {
      StandingOrdersApi.update({id:$scope.selected.id}, $scope.selected,
        function(response)
        {
          alert("Updated Successfully");
        }
      );
    }
  }

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
    link: function (scope) {
      scope.id_name      = "standing_order_id";
      scope.predicate = "name";
      if (scope.name != undefined) {
        scope.id_name = scope.name;
      }
    }
    , controller: "StandingOrderController"
  };
});