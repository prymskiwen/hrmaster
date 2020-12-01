angular.module('myApp', ['ngMaterial', 'jkAngularCarousel'])

.controller('MyCtrl', function($scope) {
    $scope.dataArray = [
     

      {
        src: '../images/fingertips.jpg'
      },
      {
        src: '../images/eLearning.jpg'
      },
      {
        src: '../images/workplace.jpg'
      },
      {
        src: '../images/reports.jpg'
      },
      {
        src: '../images/safety.jpg'
      }
    ];
});
