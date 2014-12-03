feenance.controller('PayeeController', function($scope, $http, PayeesApi) {
  // Set the default for the Form!
  $scope.selected = undefined;
  $scope.selected_id = null;
  $scope.payee_id = null;
  $scope.editing = false;


  $scope.select = function($id) {
    var record = PayeesApi.get({id:$id}, function() {
      $scope.selected = record;
      $scope.editing = false;
      $scope.selected_id  = $scope.selected.id;
    });
  };

  $scope.onSelect = function($item) {
    $scope.selected_id = $item.id;
    $scope.$emit('payeeUpdated', $item);
  };

  $scope.$on('setPayee', function (event, payee_id) {
    $scope.select(payee_id);
  });


  $scope.lookupRecords = function($viewValue) {
    return $http.get($API_ROOT + "payees/"+$viewValue).then(function(response) {
      return response.data.data;
    });
  };

  $scope.cancel = function () {
    $scope.select($scope.selected.id);
  };

  $scope.edit = function () {
    var editRecord = PayeesApi.get({id:$scope.selected.id}, function() {
      $scope.selected = editRecord;
      $scope.editing = true;
      $scope.selected_id = $scope.selected.id;
    });
  };

  $scope.save = function ($name) {
    PayeesApi.update({id:$scope.selected.id}, $scope.selected, function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  };

  $scope.add = function () {
    $record = new PayeesApi();
    $record.name = $scope.selected;
    $record.$save(function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  };

  $scope.$watch('selected.id',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        $scope.payee_id = $scope.selected.id;
      }
    }
  );

  function isSelected(payee_id) {
    return $scope.selected != undefined && $scope.selected.id == payee_id;
  }

  $scope.$watch('payee_id',
    function(new_val, old_val) {
      if (new_val != undefined && new_val != old_val) {
        if (!isSelected(new_val))
          $scope.select(new_val);
      }
    }
  );


});

feenance.directive('payeeSelector', function() {
  return {
    restrict: 'E',

    scope: {
      selected: "=ngModel"
      , payeeId: "=" // remember payee_id in markup payeeId in directive / controller ???

    }
    , templateUrl: '/view/payee_selector.html'
    , link: function (scope, element, attr) {
      if (scope.payeeId) {
        scope.select(scope.payeeId);
      }
    }
    , controller: "PayeeController"
  };
});

feenance.directive('payeeIdSelector', function() {
  return {
    restrict: 'E',

    scope: {
      payee_id: "=ngModel"

    }
    , templateUrl: '/view/payee_selector.html'
    , link: function (scope, element, attr) {
      if (scope.payee_id) {
        scope.select(scope.payee_id);
      }
    }
    , controller: "PayeeController"
  };
});


feenance.directive('payee', function(PayeesApi) {
  return {
    restrict: 'E',
    scope: {
      payeeid: "="
    , payee: "=ngModel"
    },
    templateUrl: '/view/payee.html'
    , link: function (scope) {
      if (scope.payeeid) {
        var $payee = PayeesApi.get({id:scope.payeeid}, function() {
          scope.payee= $payee;
        });
      }
    }
  };
});