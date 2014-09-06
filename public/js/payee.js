feenance.controller('PayeeController', function($scope, $http, PayeesApi) {
  // Set the default for the Form!
  $scope.selected = undefined;
  $scope.selected_id = null;
  $scope.editing = false;


  $scope.select = function($id) {
    var payee = PayeesApi.get({id:$id}, function() {
      $scope.selected = payee;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  }

  $scope.onSelect = function($item) {
    $scope.selected_id = $item.id;
  }

  $scope.lookupPayees = function($viewValue) {
    return $http.get($API_ROOT + "payees/"+$viewValue).then(function(response) {
      return response.data.data;
    });
  };
  $scope.cancel = function () {
    $scope.select($scope.selected.id);
  }

  $scope.edit = function () {
    var editRecord = PayeesApi.get({id:$scope.selected.id}, function() {
      $scope.selected = editRecord;
      $scope.editing = true;
      $scope.selected_id = $scope.selected.id;
    });
  }

  $scope.save = function ($name) {
    PayeesApi.update({id:$scope.selected.id}, $scope.selected, function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  }

  $scope.add = function () {
    $record = new PayeesApi();
    $record.name = $scope.selected;
    $record.$save(function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  }

  function getPage($page) {
    $payees = PayeesApi.get({page: $page}, function() {
      $scope.payees = $scope.payees.concat($payees.data);
      if ($payees.paginator != undefined && $payees.paginator.next != undefined) {
        getPage($payees.paginator.next);
      }
    });
  }
  $scope.payees = [];
  getPage(1);
});

feenance.directive('payeeSelector', function(PayeesApi, $compile) {
  return {
    restrict: 'E',
    scope: {
      selected_id: "=ngModel"
      , payeeId: "=" // remember payee_id in markup payeeId in directive / controller ???
    },
    templateUrl: 'newPayee.html'
    , link: function (scope, element, attr) {
      if (scope.payeeId) {
        scope.select(scope.payeeId);
      }
    }
    , controller: "PayeeController"
  };
});