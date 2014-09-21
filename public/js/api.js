$API_ROOT = "http://feenance/api/v1/";

feenance.factory('AccountsApi', function($resource) {
  return $resource(   $API_ROOT + "accounts/:id/:collection",   {id:"@id"}
    , {
      'transactions': { method:'GET', params: {collection: "transactions" } },
        'update':       { method:'PUT'                    }
    }
  );
});

feenance.factory('PayeesApi', function($resource) {
  return $resource(   $API_ROOT + "payees/:id",   {id:"@id"}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('CategoriesApi', function($resource) {
  return $resource(   $API_ROOT + "categories/:id",   {id:"@id"}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('TransactionsApi', function($resource) {
  return $resource(   $API_ROOT + "transactions/:id",   {id:"@id"}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});

feenance.factory('BankStringsApi', function($resource) {
  return $resource(   $API_ROOT + "bank_strings/:id/:collection",   {id:"@id"}
    ,   {
      'update':       { method:'PUT'                    },
      'transactions': { method:'GET',   params: {collection: "transactions" } },
      'map':          { method:'POST',  params: {collection: "transactions" } }

    }
  );
});

