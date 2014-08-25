var snodbert = angular.module("feenance", ['ngResource', 'ngRoute', 'leaflet-directive', 'ui.bootstrap', 'ngCookies']);

snodbert.factory('Notifier', function () {
  var observerCallbacks = {};
  return {
    onChange: function (name, callback) {
      if (undefined === observerCallbacks[name] )
        observerCallbacks[name] = [];
      observerCallbacks[name].push(callback);
    },
    notify: function (name, param) {
      if (undefined === observerCallbacks[name] ) return; // nobody is listening :(
      angular.forEach(observerCallbacks[name], function (callback) {
        callback(param);
      });
    }
  };
});

snodbert.factory('CurrentUser', function(CurrentUserApi, Notifier) {
  var user      = false;
  var checked_in  = false;
  var checked_out  = false;
  this.getUser = function(forceLocation) {
    var fetchedUser     = CurrentUserApi.get( function () {
      user = fetchedUser;
      if (forceLocation) {
        user.location_id = forceLocation;
      }
      checked_in = user.location_id;
      Notifier.notify('CurrentUser'); // Tells Locations that the user has changed
    });
  };

  this.getUser();

  this.checkin = function (location_id) {
    checked_out = checked_in;
    checked_in  = location_id;
    user.location_id = location_id;
    user.$update();
    this.getUser(user.location_id);
  };

  return {
      onChange:     function(callback)  { Notifier.onChange('CurrentUser', callback);   }
    , user:         function()          { return user;        }
    , checkin:      this.checkin
    , getUser:      this.getUser
    , checked_in:   function()          { return checked_in;  }
    , checked_out:  function()          { return checked_out;  }
    , isCheckedIn:  function(location_id)       { return location_id == checked_in;  }
  };
});


snodbert.factory('CurrentUserApi', function($resource) {
  return $resource(   "http://snodbert/api/v1/users/current/:resource"
    , {}
    , { 'update': { method:'PUT' }  }
  );
});

snodbert.factory('User', function($resource) {
  return $resource(   "http://snodbert/api/v1/users/:id/",   {});
});

snodbert.factory('LocationApi', function($resource) {
  return $resource(   "http://snodbert/api/v1/locations/:id:lat1/:lng1/:lat2/:lng2",   {}
    ,   {
          'closest':  { method:'GET', params: {id: "" } } // get closest locations to http://snodbert/api/v1/locations/lat1/lng1
        , 'bounds':   { method:'GET', params: {id: ""} } // get all locations within region to http://snodbert/api/v1/locations/lat1/lng1/lat2/lng2
        , 'update':   { method:'PUT'                    }
        }
  );
});



// Initialise a factory for each of the Node types
function registerNodeFactory(name, root) {
    snodbert.factory(name+"Api", function($resource) {
        return $resource
        (   "http://snodbert/api/v1/"+root+"/:id/:filterid",   {}
            ,   {   'update': { method:'PUT' }
                }
        );
    });
}

registerNodeFactory('Snod', 'snods'); //SnodController = function(Snod, $scope) {Snod.Controller($scope);}
registerNodeFactory('Bert', 'berts');
//registerNodeFactory('Location', 'locations');
registerNodeFactory('Network', 'networks');
