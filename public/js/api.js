$API_ROOT = "http://feenance/api/v1/";

feenance.factory('AccountsApi', function($resource) {
//  return $resource(   "http://snodbert/api/v1/locations/:id:lat1/:lng1/:lat2/:lng2",   {}

  return $resource(   $API_ROOT + "accounts/:id/:collection",   {}
    ,   {
      'transactions':  { method:'GET', params: {collection: "transactions" } } // get closest locations to http://snodbert/api/v1/locations/lat1/lng1
      , 'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('TransactionsApi', function($resource) {
  return $resource(   $API_ROOT + "transactions",   {}
    ,   {
       'update':   { method:'PUT'                    }
    }
  );
});

