feenance.factory('CategoryReportData', function(Notifier, $http) {
  var data = {};
  function set(year) {
    $http.get('/api/v1/transactions/totals/categories/' + year)
      .success(function(response) {
        data     = response.total;
        Notifier.notify('CategoryReportData', data);
      });
  };

  function getCatMonth(category_id, month)  {
    try {
      return data.categories[category_id].months[month];
    } catch(e) {
    }
    return {};
  }

  set(2014);
  return {
    set: set
    , get:          function () {   return data;    }
    , getCatMonth: getCatMonth
    , onChange: function (callback) { Notifier.onChange('CategoryReportData', callback); }
  }
});


feenance.controller('CategoryReportController', function($scope, CategoryReportData) {
  $scope.data     = {};
  CategoryReportData.onChange(function(){
    $scope.data = CategoryReportData.get();
  });
});

feenance.directive('categoryReport', function() {
  return {
    restrict: 'E',
    scope: {    },
    templateUrl: 'view/categoryReport.html'
    , link: function (scope) {
    }
    , controller: "CategoryReportController"
  };
});

feenance.directive('categoryReportCell', function(CategoryReportData) {
  return {
    restrict: 'E',
    scope: {
      categoryId: "=",
      month: "="
    },
    template: '{{cellData.credit_total}} <br/>{{cellData.debit_total}} <br/>{{cellData.net_total}}',
    link: function (scope) {
      scope.cellData = CategoryReportData.getCatMonth(scope.categoryId, scope.month);
    }
  };
});

