feenance.directive('jsonView', function() {
  return {
    restrict: 'E'
    , scope: {
        jsonObject: "=ngModel"
      , title: "@"
    }
    , template: '<pre ng-show="debug">{{title}}: {{jsonObject | json}}</pre>'
    , link: function (scope, element, attr, controller) {
      if (attr.debug !=undefined) {
        scope.debug=attr.debug;
      }
      scope.$on('setDebug', function($event, value) {
        scope.debug=value;
        });
      }
    , controller: 'FeenanceController'
  };
});