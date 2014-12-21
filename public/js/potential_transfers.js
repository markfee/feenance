/**
 * Created by mark on 18/12/14.
 */

feenance.factory('PotentialTransfersApi', function($resource) {
    return $resource(   $API_ROOT + "transfers/potential", { }, {
            'save':     { method:'POST' }
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

    $scope.removeTransfer = function(item, $index)
    {
        $scope.items.splice($index, 1);
    }

    $scope.sendAll = function()
    {
        var $data = { data: $scope.items };

        PotentialTransfersApi.save(
            {},
            $data,
            function(response)
            {
                alert("Success");
            },
            function (response)
            {
                alert("Failed");
            }
        );
    }
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