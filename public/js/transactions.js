feenance.factory('TransactionCollection', function(TransactionsApi, Collection) {
    return new Collection(TransactionsApi, "<transactions...>");
});

feenance.controller('TransactionsController', function($scope, TransactionCollection, CollectionSelection)
{
    var collectionSelection = new CollectionSelection(TransactionCollection, $scope, "id");

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

