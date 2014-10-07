feenance.controller('CategoryReportController', function($scope, $http) {
  $scope.data = $http.get('/api/v1/transactions/totals/categories/2014').success(function(){
    alert("YAYY");
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

