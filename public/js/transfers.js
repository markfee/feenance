feenance.factory('TransfersApi', function($resource) {

    return $resource(   $API_ROOT + "transfers/:id", {  id:"@id" }, { }    );
});

feenance.controller('TransfersController', function($scope, TransfersApi)
{
    $scope.predicate="date";
    $scope.reverse = false;

    $scope.items = [];
    var items = TransfersApi.get(
        {},
        function ()
        {
            $scope.items = items.data;
        }
    );

    $scope.sort = function(predicate) {
        if ($scope.predicate == predicate) {
            $scope.reverse=!$scope.reverse;
        } else {
            $scope.predicate = predicate;
            $scope.reverse=false;
        }
    };

});

feenance.directive('transfersTable', function() {
    return {
        restrict: 'E',
        scope: {    },
        templateUrl: '/view/transfers_table.html'
        , link: function (scope) {
        }
        , controller: "TransfersController"
    };
});