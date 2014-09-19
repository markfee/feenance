feenance.controller('CategoryController', function($scope, $http, CategoriesApi) {
  // Set the default for the Form!
  $scope.selected = undefined;
  $scope.selected_id = null;
  $scope.editing = false;


  $scope.select = function($id) {
    var record = CategoriesApi.get({id:$id}, function() {
      $scope.selected = record;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
      $scope.$emit('categoryUpdated', record);
    });
  };

  $scope.onSelect = function($item) {
    $scope.selected_id = $item.id;
    $scope.$emit('categoryUpdated', $item);
  };

  $scope.$on('setCategory', function (something, category_id) {
    $scope.select(category_id);
  });

  $scope.lookupRecords = function($viewValue) {
    return $http.get($API_ROOT + "categories/"+$viewValue).then(function(response) {
      return response.data.data;
    });
  };

  $scope.cancel = function () {
    $scope.select($scope.selected.id);
  };

  $scope.edit = function () {
    var editRecord = CategoriesApi.get({id:$scope.selected.id}, function() {
      $scope.selected = editRecord;
      $scope.editing = true;
      $scope.selected_id = $scope.selected.id;
    });
  };

  $scope.save = function ($name) {
    CategoriesApi.update({id:$scope.selected.id}, $scope.selected, function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  };

  $scope.add = function () {
    $record = new CategoriesApi();
    $record.name = $scope.selected;
    $record.$save(function(response) {
      $scope.selected = response;
      $scope.editing = false;
      $scope.selected_id = $scope.selected.id;
    });
  };

  function getPage($page) {
    $records = CategoriesApi.get({page: $page}, function() {
      $scope.records = $scope.records.concat($records.data);
      if ($records.paginator != undefined && $records.paginator.next != undefined) {
        getPage($records.paginator.next);
      }
    });
  }
//  $scope.records = [];
//  getPage(1);
});

feenance.directive('categorySelector', function() {
  return {
    restrict: 'E',
    scope:
    {
      selected: "=ngModel"
    , categoryId: "=" // remember category_id in markup categoryId in directive / controller ???
    }
  , templateUrl: 'view/categorySelector.html'
  , link: function (scope, element, attr) {
      if (scope.categoryId) {
        scope.select(scope.categoryId);
      }
    }
  , controller: "CategoryController"
  };
});