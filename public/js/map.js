feenance.controller('MapController', function($scope, MapsApi) {
  $scope.reset = function() {
    $scope.map = new MapsApi();
    $scope.map.category_id  = null;
    $scope.map.account_id   = null;
    $scope.map.transfer_id  = null;
    $scope.map.payee_id     = null;
    $scope.map.account      = null;
    $scope.map.transfer     = null;
    $scope.map.payee        = null;
  };
  $scope.success        = null;
  $scope.reset();

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

  $scope.add = function(map) {
    $scope.map.$save( function(response) {
        $scope.success = "Saved Successfully";
        // Make sure that an array of newMaps is emitted - even if it's just one.
        $maps = (response.data ? response.data :  [response]);
        $scope.$emit("newMap", $maps);
//        $scope.reset();
        $scope.map = $maps[0];
      } , function(response) {
        $scope.success = response.data.errors.error[0];
      }
    );
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