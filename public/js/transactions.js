feenance.factory('TransactionCollection', function (TransactionsApi, Collection)
{
    return (new Collection(TransactionsApi, "<transactions...>")).fetchAll();
});

feenance.controller('TransactionsController', function ($scope, TransactionCollection, CollectionSelection, $filter)
{
    var collectionSelection = new CollectionSelection(TransactionCollection, $scope, "id");

    $scope.currentPage = 0;
    $scope.pageSize = 20;

    $scope.getCurrentPage = function ()
    {
        try {
            return $scope.offsetPage(0);
        } catch(e) { }
        return 0;
    }

    $scope.offsetPage = function (offset)
    {
        $scope.currentPage =  Math.max( Math.min($scope.currentPage + offset, $scope.numberOfPages() - 1), 0) ;
        return $scope.currentPage;
    }

    $scope.numberOfPages = function ()
    {
        try {
            return Math.ceil($scope.filteredRecordCount() / $scope.pageSize);
        } catch(e) { }
        return 0;
    };

    $scope.firstRecordToDisplay = function ()
    {
        return $scope.currentPage*$scope.pageSize;
    }

    $scope.lastRecordToDisplay = function ()
    {
        return Math.min($scope.firstRecordToDisplay() + $scope.pageSize, $scope.filteredRecordCount());
    }

    $scope.filteredRecordCount = function()
    {
        return $filter('filter')($scope.boundCollection, $scope.transactionFilter).length;
    }

    $scope.transactionFilter = function (transaction)
    {
        try {
            if ($scope.account_id && transaction.account_id != $scope.account_id)
            {
                return false;
            }
            return $scope.reconciled_filter && $scope.reconciled_filter(transaction);
        } catch(e) { }
        return true;
    };
});

feenance.directive('transactionsTable', function ()
{
    return {
        restrict: 'E',
        scope: {},
        templateUrl: '/view/transaction_table.html'
        , link: function (scope)
        {
            scope.pageSize = 20;
        }
        , controller: "TransactionsController"
    };
});

