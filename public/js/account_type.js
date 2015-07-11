feenance.factory('AccountTypeCollection', function(AccountTypesApi, Collection) {
    return (new Collection(AccountTypesApi, "<Please select an account type>")).fetchAll();
});

feenance.controller('AccountTypeController', function($scope, AccountTypeCollection, CollectionSelection) {
    var collectionSelection = new CollectionSelection(AccountTypeCollection, $scope, "account_type_id");
    $scope.predicate = ["name"];
});

feenance.directive('accountTypeSelector', function() {
    return {
        restrict: 'E',
        scope: {
            account_type_id: "=ngModel", ngModel: "="
        },
        template: '' +
        '<div class="form-group"> ' +
        '   <label>Account Type</label>  ' +
        '   <div class="input">' +
        '       <select ng-options="account_type.id as account_type.name for account_type in boundCollection " ng-model="account_type_id"> </select>' +
        '   </div>' +
        '</div>',
        link: function (scope, element, attr)
        {
            if (scope.ngModel) {
                scope.selectItem(scope.ngModel);
            }
        },
        controller: "AccountTypeController"
    };
});