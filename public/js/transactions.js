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
        return $scope.offsetPage(0);
    }

    $scope.offsetPage = function (offset)
    {
        $scope.currentPage =  Math.min(Math.max($scope.currentPage + offset, 0), $scope.numberOfPages() - 1);
        return $scope.currentPage;
    }

    $scope.numberOfPages = function ()
    {
        return Math.ceil($scope.filteredRecordCount() / $scope.pageSize);
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
        if ($scope.account_id && transaction.account_id != $scope.account_id)
        {
            return false;
        }
        return $scope.reconciled_filter(transaction);
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

