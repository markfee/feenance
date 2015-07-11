feenance.factory('Categories', function (Notifier, $http, Paginator) {
    var data = []; // all of the data returned as an array.
    var categories = {}; // the specific categories accessible by id.

    function fetchAll($page) {
        $http.get($API_ROOT + 'categories?page=' + $page)
            .success(function (response) {
                data = data.concat(response.data);
                angular.forEach(response.data, function (category, key) {
                    categories[category.id] = category;
                });
                var nextPage = Paginator.nextPage(response);
                if (nextPage) {
                    fetchAll(nextPage);
                } else {
                    Notifier.notify('Categories', data);
                }
            });
    };

    fetchAll(1);
    return {
        data: function () {
            return data;
        },
        categories: function () {
            return categories;
        },
        getCategory: function (id) {
            return (id == 'UNKNOWN') ? {fullName: 'Not Set'} : categories[id];
        },
        onChange: function (callback) {
            Notifier.onChange('Categories', callback);
        }
    }
});


feenance.controller('CategoryController', function ($scope, $http, CategoriesApi) {
    // Set the default for the Form!
    $scope.init = function() {
        $scope.selected = undefined;
        $scope.selected_id = null;
        $scope.category_id = null;
        $scope.editing = false;
    }

    $scope.init();

    $scope.select = function ($id) {
        var record = CategoriesApi.get({id: $id}, function () {
            $scope.selected = record;
            $scope.editing = false;
            $scope.selected_id = $scope.selected.id;
            $scope.$emit('categoryUpdated', record);
        });
    };

    $scope.onSelect = function ($item) {
        $scope.selected_id = $item.id;
        $scope.$emit('categoryUpdated', $item);
    };

    $scope.$on('setCategory', function (something, category_id) {
        $scope.select(category_id);
    });

    $scope.lookupRecords = function ($viewValue) {
        return $http.get($API_ROOT + "categories/" + $viewValue).then(function (response) {
            return response.data.data;
        });
    };

    $scope.cancel = function () {
        $scope.select($scope.selected.id);
    };

    $scope.edit = function () {
        var editRecord = CategoriesApi.get({id: $scope.selected.id}, function () {
            $scope.selected = editRecord;
            $scope.editing = true;
            $scope.selected_id = $scope.selected.id;
        });
    };

    $scope.save = function ($name) {
        CategoriesApi.update({id: $scope.selected.id}, $scope.selected, function (response) {
            $scope.selected = response;
            $scope.editing = false;
            $scope.selected_id = $scope.selected.id;
        });
    };

    $scope.add = function () {
        $record = new CategoriesApi();
        $record.name = $scope.selected;
        $record.$save(function (response) {
            $scope.selected = response;
            $scope.editing = false;
            $scope.selected_id = $scope.selected.id;
        });
    };

    function getPage($page) {
        $records = CategoriesApi.get({page: $page}, function () {
            $scope.records = $scope.records.concat($records.data);
            if ($records.paginator != undefined && $records.paginator.next != undefined) {
                getPage($records.paginator.next);
            }
        });
    }

    $scope.$watch('selected.id',
        function (new_val, old_val) {
            if (new_val != undefined && new_val != old_val) {
                $scope.category_id = $scope.selected.id;
            }
        }
    );

    function isSelected(category_id) {
        return $scope.selected != undefined && $scope.selected.id == category_id;
    }

    $scope.$watch('category_id',
        function (new_val, old_val) {
            if (new_val != undefined && new_val != old_val) {
                if (!isSelected(new_val))
                    $scope.select(new_val);
            } else if (new_val == undefined) {
                $scope.init();
            }
        }
    );


});

feenance.directive('categorySelector', function () {
    return {
        restrict: 'E',

        scope: {
            selected: "=ngModel"
            , categoryId: "=" // remember category_id in markup categoryId in directive / controller ???

        }
        , templateUrl: '/view/category_selector.html'
        , link: function (scope, element, attr) {
            if (scope.categoryId) {
                scope.select(scope.categoryId);
            }
        }
        , controller: "CategoryController"
    };
});

feenance.directive('categoryIdSelector', function () {
    return {
        restrict: 'E',
        scope: {
            category_id: "=ngModel"
        }
        , templateUrl: '/view/category_selector.html'
        , link: function (scope, element, attr) {
            if (scope.category_id) {
                scope.select(scope.category_id);
            } else {
                scope.init();
            }
        }
        , controller: "CategoryController"
    };
});


feenance.directive('category', function (Categories) {
    return {
        restrict: 'E',
        scope: {
            categoryId: "=" // remember category_id in markup categoryId in directive / controller ???
        }
        , template: '{{category.fullName}}'
        , link: function (scope, element, attr) {
            function getData() {
                if (scope.categoryId) {
                    scope.category = Categories.getCategory(scope.categoryId);
                }
            }

            getData();
            Categories.onChange(function () {
                getData();
            });
        }
    };
});
