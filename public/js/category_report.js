feenance.factory('CategoryReportData', function(Notifier, $http) {
  var data = {};
  function set(year) {
    $http.get($API_ROOT+'transactions/totals/categories/' + year)
      .success(function(response) {
        data     = response.total;
        Notifier.notify('CategoryReportData', data);
      });
  };

  function getCatMonth(category_id, month) {
    try {
      if (month)
        return data.categories[category_id].months[month];
      else
        return data.categories[category_id];
    } catch(e) {
    }
    return {};
  }

//  set(2014);
  return {
    set: set
    , get:          function () {   return data;    }
    , getCatMonth: getCatMonth
    , onChange: function (callback) { Notifier.onChange('CategoryReportData', callback); }
  }
});


feenance.controller('CategoryReportController', function($scope, CategoryReportData, Categories) {
  $scope.data     = {};

  CategoryReportData.onChange(function() {
    $scope.categories = [];
    $scope.data = CategoryReportData.get();
    angular.forEach($scope.data.categories, function(category, key) {
      $scope.categories.push(category);
    });
  });

  function setCategoryDetails() {
    angular.forEach($scope.categories, function(category, key) {
      angular.extend(category, Categories.getCategory(category.category_id));
//      $scope.categories.push(category);
    });

  }

  Categories.onChange(function() {
    setCategoryDetails();
  });

  $scope.creditFilter = function(element) {
    return element.credit_total >  0 ? true : false;
  };

  $scope.debitFilter = function(element) {
    return element.debit_total >  0 ? true : false;
  };

  $scope.predicate    = ["fullName"];
  $scope.reverse      = false;
  $scope.sort = function(predicate) {
    if ($scope.predicate == predicate) {
      $scope.reverse=!$scope.reverse;
    } else {
      $scope.predicate = predicate;
      $scope.reverse=false;
    }
  };

});

feenance.directive('categoryReport', function(CategoryReportData) {
  return {
    restrict: 'E',
    scope: {
      year: "="
    },
    templateUrl: '/view/categoryReport.html'
    , link: function (scope) {
      if (!scope.year) scope.year = 2014;
      CategoryReportData.set(scope.year);
    }
    , controller: "CategoryReportController"
  };
});

//template: '{{cellData.credit_total}} <br/>{{cellData.debit_total}} <br/>{{cellData.net_total}}',


feenance.directive('categoryReportCell', function(CategoryReportData) {
  return {
    restrict: 'E',
    scope: {
      categoryId: "=",
      month: "=",
      val: "@"
    },
    template: '<span ng-show="my_value">{{my_value  | currency: "£" }}</span>',
    link: function (scope) {
      scope.cellData = CategoryReportData.getCatMonth(scope.categoryId, scope.month);
      try {
        scope.my_value = (
          scope.val == "credit" ? scope.cellData.credit_total
        : scope.val == "debit"  ? scope.cellData.debit_total
        : scope.cellData.net_total);
      } catch(e) {
        scope.my_value = "";
      }
    }
  };
});
