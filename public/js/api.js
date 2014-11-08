$API_ROOT = "/api/v1/";

feenance.factory('AccountsApi', function($resource) {
  return $resource(   $API_ROOT + "accounts/:id/:collection/:filter",   {id:"@id"}, {
      'transactions': { method:'GET', params: {collection: "transactions" } },
      'update':       { method:'PUT'                    },
      'deleteUnreconciled': { method:'DELETE', params: {collection: "transactions", filter: "unreconciled" } },
      'reconcileAll': { method:'POST', params: {collection: "transactions", filter: "reconcile" } }
    }
  );
});

feenance.factory('StandingOrdersApi', function($resource) {
  return $resource(   $API_ROOT + "standing_orders/:id/:collection", { id:"@id"}, {
      'transactions': { method:'GET', params: {collection: "transactions" } },
      'update':       { method:'PUT'                    },
      'increment':    { method:'PUT', params: {collection: "increment" } }
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

