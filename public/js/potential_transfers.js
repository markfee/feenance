/**
 * Created by mark on 18/12/14.
 */

feenance.factory('PotentialTransfersApi', function($resource) {
    return $resource(   $API_ROOT + "transfers/potential", { }, {
            'potential':    { method:'GET', params: {collection: "potential" } },
            'update':       { method:'PUT'                    }
        }
    );
});

feenance.factory('PotentialCollection', function(PotentialTransfersApi, Collection) {
    return new Collection(PotentialTransfersApi, "<...transfers>");
});


feenance.controller('PotentialTransfersController', function($scope, PotentialTransfersApi) {
    $scope.items = [];
     var items = PotentialTransfersApi.get(
        { },
        function()
        {
            $scope.items = items.data;
        }
    );
});

feenance.directive('potentialTransfersTable', function() {
        return {
            restrict: 'E',
            scope: {    },
            templateUrl: '/view/potential_transfers_table.html'
            , link: function (scope) {
            }
            , controller: "PotentialTransfersController"
        };
    });