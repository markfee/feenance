feenance.factory('TransfersApi', function($resource) {

    return $resource(   $API_ROOT + "transfers/:id", {  id:"@id" }, { }    );
});

feenance.controller('TransfersController', function($scope, TransfersApi)
{
    $scope.predicate="-date";
    $scope.reverse = false;

    $scope.items = [];

    var getPage = function($page)
    {
        var items = TransfersApi.get(
            {perPage: 50, page: $page},
            function ()
            {
                for (var i = 0; i < items.data.length; i++)
                {

                    items.data[i].page = $page;
                    $scope.items.push(items.data[i]);
                }
                if (items.paginator && items.paginator.next) {
                    getPage(items.paginator.next);
                }
            }
        );
    };

    getPage(1);

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