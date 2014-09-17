$API_ROOT = "http://feenance/api/v1/";

feenance.factory('AccountsApi', function($resource) {
  return $resource(   $API_ROOT + "accounts/:id/:collection",   {}
    ,   {
      'transactions':  { method:'GET', params: {collection: "transactions" } }
      , 'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('PayeesApi', function($resource) {
  return $resource(   $API_ROOT + "payees/:id",   {}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('CategoriesApi', function($resource) {
  return $resource(   $API_ROOT + "categories/:id",   {}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('TransactionsApi', function($resource) {
  return $resource(   $API_ROOT + "transactions/:id",   {}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('MapsApi', function($resource) {
  return $resource(   $API_ROOT + "maps/:id",   {}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

