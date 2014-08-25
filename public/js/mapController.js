snodbert.factory('Home', function ($cookieStore, Notifier) {
  var home = {};

  function validate($home){
    if ($home === null || typeof $home !== 'object')  return false;
    if (undefined == $home.lat)    return false;
    if (undefined == $home.lng)    return false;
    if (undefined == $home.zoom || $home.zoom < 14) // 14 === Pickering zoom level
      $home.zoom = 14;
    return $home;
  }
  function setHome(newHome) {
    newHome = validate(newHome);
    if (newHome) {
      home = newHome;
    } else if (newHome = validate($cookieStore.get('home'))) {
      home = newHome;
    } else {
      home = {
          lat: 54.248
        , lng: -0.775180
        , zoom:  8
      }
    }
    $cookieStore.put('home', home);
    Notifier.notify('Home', home);
    return home;
  }

  setHome();
  return {
      set: setHome
    , get: function () {      return home;    }
    , onChange: function (callback) { Notifier.onChange('Home', callback); callback(home);    }
  }
});

snodbert.factory('Locations', function (LocationApi, $cookieStore, Home, Notifier, CurrentUser, $log) {
  var locations = [];

  function fetchLocations() {
    var home = Home.get();
    var fetchedLocations = LocationApi.closest({lat1: home.lat, lng1: home.lng},
      function () {
        angular.forEach(fetchedLocations.data, function (location) {
          locations[location.id] = location;
        });
        Notifier.notify('Locations', locations); // Tell the Map and any views to update
      });
  }

  function fetchBounds(lat1, lng1, lat2, lng2, page ) {
    // NB if page is not set then LocationApi does not add it to it's query string
    var fetchedLocations = LocationApi.bounds({lat1: lat1, lng1: lng1, lat2: lat2, lng2: lng2, page: page},
      function () {
//        $log.log("Page " + fetchedLocations.paginator.current_page + " of " + fetchedLocations.paginator.last_page);
        angular.forEach(fetchedLocations.data, function (location) {
          locations[location.id] = location;
        });
        if (fetchedLocations.paginator && fetchedLocations.paginator.next) {
          fetchBounds(lat1, lng1, lat2, lng2, fetchedLocations.paginator.next);
        }
        Notifier.notify('Locations', locations); // Tell the Map and any views to update
      });
  }


  function add(id, callback) {
    var fetchedLocation = LocationApi.get({id: id},
      function () {
        locations[location.id] = fetchedLocation;
        if (callback) {
          callback(locations[location.id]);
        } else {
          Notifier.notify('Locations', [locations[location.id]]); // Tell the Map and any views to update
        }
      });
  }
  // WHEN THE CURRENT USER CHANGES WE ADD THE NEW LOCATION, UPDATE HOME, AND NOTIFY THAT NEW AND OLD LOCATIONS HAVE CHANGED
  CurrentUser.onChange(function () {
    if (CurrentUser.checked_in()) {
      add(CurrentUser.checked_in()
        , function(newHome) {
          Home.set(newHome); // This will cause all locations to be redrawn by the onChange event.
        }
      );
    }
  });

  Home.onChange(function() {
    fetchLocations();
  });

  return {
      onChange: function (callback) { Notifier.onChange('Locations', callback); callback(locations);    }
    , locations: function ()  {      return locations;      }
    , location: function (id) {      return locations[id];  }
    , add: add
    , fetchBounds: fetchBounds
  }
});

snodbert.directive('locationPopupDetail', function() {
  return {
      templateUrl: "locationPopupDetail.html"
    , replace: false
    , controller: locationPopupDetailCtrl
  };
});

locationPopupDetailCtrl = function ($scope, Locations, CurrentUser, Map) {
  console.log("Compiling: "  + $scope.location_id);
  try {
    // Make the map scroll to display the full popup
    $diff         =  $('.leaflet-popup-content').offset().top - $("#mapWrapper").offset().top;
    if ($diff < 0) {
      Map.panBy(new L.Point(0, $diff));
    }
  } catch($ex){ console.log("Pan Map Error "); };

  var location = Locations.location($scope.location_id);
  $scope.location   = location;
  $scope.checkedIn  = CurrentUser.isCheckedIn(location.id);
  $scope.user = CurrentUser.user();
  $scope.checkin = function() {
    CurrentUser.checkin($scope.location.id);
    Map.closePopup();
  }
}

snodbert.factory('Map', function(leafletData) {
  var map = null;
  leafletData.getMap().then(function(themap) {
    map = themap;
  });
  return {
      map: function() { return map; }
    , closePopup: function() { map.closePopup(); }
    , panBy: function($point) { map.panBy($point, {animate: false});  } // TODO fix bug causing a jump
  };
});


snodbert.controller('MapController', function ($scope, CurrentUser, Locations, $modal, Home, LocationPopup, leafletData, $compile, $rootScope, $log, $interval) {
// TODO: get browser location & smooth scroll to checked in location
// Set Some defaults first (otherwise this doesn't work);
  var $home = Home.get();

  angular.extend($scope, {
    home: $home, defaults: { scrollWheelZoom: false    }, markers: {    }, events: {
      map: {
        enable: ['click', 'popupopen', 'popupopen', 'zoomend', 'moveend'],
        logic: 'emit'
      }
    }
  });

  var popupcounter = 0;

  $scope.$on('leafletDirectiveMap.click', function (event, args) {
    if (popupcounter <= 0) {
      LocationPopup.showPopup(args.leafletEvent.latlng);
    }
  });

  $scope.$on('leafletDirectiveMap.popupclose', function (e, popup) {
    // Delay the popup counter for 250ms to avoid close clicks opening a new item dialog
    $interval(function() { --popupcounter; }, 250, 1, false);
  });

  /*
  Compile and show the pop up for a marker dynamically
   */
  $scope.$on('leafletDirectiveMap.popupopen', function (e, popup) {
    popupcounter = 1;
    var $elem = angular.element('.leaflet-popup-content-wrapper').children(":first").children(":first");
    var scope = $rootScope;
    scope.location_id  = $elem.data("location_id");
    scope.popup = popup.leafletEvent.popup;
    $compile($elem)(scope);
    $elem.height("auto");
    angular.element(".leaflet-popup-content").width("auto");
  });

  $scope.$on('leafletDirectiveMap.zoomend', function (e, zoomevent) {    mapChange();  });
  $scope.$on('leafletDirectiveMap.moveend', function (e, zoomevent) {    mapChange();  });

  function mapChange() {
    leafletData.getMap().then(function(map) {
      var bounds = map.getBounds();
      var northEast = bounds.getNorthEast().wrap();
      var southWest = bounds.getSouthWest().wrap();
      if (console)    console.log("Get Locations within these bounds: " + southWest.lat +  " < lat <  " + northEast.lat + " AND " + southWest.lng + " < lng <  " + northEast.lng);
      Locations.fetchBounds(southWest.lat, southWest.lng, northEast.lat, northEast.lng);
    });

  }

  Locations.onChange(function (changedLocations) {
    angular.forEach(changedLocations, function (location) {
      addMarker(location);
    });
  });

  function addMarker(location) {
    $scope.markers[location.id] = locationToMarker(location);
  }

  function locationToMarker(location) {
    $log.log("locationToMarker: " + location.id);
    var $checkedIn = CurrentUser.isCheckedIn(location.id) ? true : false;
    var $content = "<div location-popup-detail data-location_id='"+location.id+"'></div>";
    return {
      lat: location.lat, lng: location.lng, message: $content, focus: $checkedIn, draggable: false, location: location
    };
  }
});

/*
function checkin(location_id) {
  if (console)    console.log("checkin(" + location_id + ")");

  var elem = angular.element(document.getElementById("CurrentUser"));
  var injector = elem.injector();                 //get the injector.
  var currentUser = injector.get('CurrentUser');  //get the service.
  currentUser.checkin(location_id);
}
*/

snodbert.factory('LocationPopup', function (Locations, $modal) {
  function showPopup(latlng) {
    function newLocation() { return latlng; }
    var popupInstance = $modal.open({
        templateUrl: 'locationPopup.html'
      , controller: LocationPopupCtrl
      , size: 'sm'
      , resolve: {  newLocation: newLocation }
    });

    popupInstance.result.then(function (new_location_id) {
      Locations.add(new_location_id);
//      MapController.fetchLocation(new_location_id);
    }, function () {
      // alert('Modal dismissed at: ' + new Date());
    });
  }

  return {
    showPopup: showPopup
  };
});

  LocationPopupCtrl = function ($scope, $modalInstance, newLocation, LocationApi) {
  $scope.location = newLocation;

  $scope.save = function () {
    var location = new LocationApi($scope.location);
    location.$save(function (response, putResponseHeaders) {
      if (response.status_code < 300) {
        $modalInstance.close(response.data[0].id);
      } else {
        alert(response.errors[0].error);
      }
    });
  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
}