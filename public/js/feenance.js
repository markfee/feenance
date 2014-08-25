var snodbert = angular.module("feenance", ['ngResource', 'ngRoute']);

$API_ROOT = "http://feenance/api/v1/";

snodbert.factory('TransactionApi', function($resource) {
  return $resource(   $API_ROOT + "transactions",   {}
    ,   {
      'update':   { method:'PUT'                    }
    }
  );
});


snodbert.controller('TransactionController', function($scope, TransactionApi) {
  var transactions = TransactionApi.get( {  },
    function () {
      $scope.items = transactions.data;
    });
});
