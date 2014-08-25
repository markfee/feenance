snodbert.controller('CurrentUserController', function($scope, CurrentUser, Locations) {
//  $scope.location = false;
//  $scope.user     = false;
  CurrentUser.onChange( function() {
    $scope.user     = CurrentUser.user();
    $scope.location = Locations.location($scope.user.location_id);
  });
  Locations.onChange( function(locations) {
    if (locations[CurrentUser.checked_in()]) {
      $scope.location = locations[CurrentUser.checked_in()];
    }
  })
});


snodbert.controller('SnodController', function($scope, SnodApi, CurrentUserApi) {
  var user = CurrentUserApi.get(function () {
    var nodes = SnodApi.get( { id: 'owner', filterid: user.id },
      function () {
        $scope.items = nodes.data;
      });
  });
});

snodbert.controller('BertController', function($scope, BertApi, CurrentUserApi) {
  var user = CurrentUserApi.get(function () {
    var nodes = BertApi.get({id: 'owner', filterid: user.id},
      function () {
        $scope.items = nodes.data;
      });
  });
});

snodbert.controller('LocationController', function($scope, LocationApi, CurrentUserApi) {
  var location  = CurrentUserApi.get(   { resource: 'location' } , function () {
    $scope.location = location;
    var nodes = LocationApi.closest({lat1: location.lat, lng1: location.lng},
      function () {
        $scope.items = nodes.data;
      });
  }  );
});
