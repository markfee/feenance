feenance.factory('TransactionCollection', function(TransactionsApi, Collection) {
    return (new Collection(TransactionsApi, "<transactions...>")).fetchAll();
});

feenance.controller('TransactionsController', function($scope, TransactionCollection, CollectionSelection)
{
    var collectionSelection = new CollectionSelection(TransactionCollection, $scope, "id");

    $scope.currentPage = 00;
    $scope.pageSize = 20;
    $scope.numberOfPages=function(){
        return Math.ceil($scope.boundCollection.length/$scope.pageSize);
    }

    $scope.filter = function(element)
    {
        return element.page <= 3;
    }
});

feenance.directive('transactionsTable', function() {
    return {
        restrict: 'E',
        scope: {    },
        templateUrl: '/view/transaction_table.html'
        , link: function (scope) {

        }
        , controller: "TransactionsController"
    };
});

