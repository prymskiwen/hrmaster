// angular.module('app', ['ngRoute', 'ngSanitize', 'ngCookies', 'ngMaterial', 'pdfjsViewer']);
var app = angular.module('app', ['pdfjsViewer','ngRoute']);

app.controller('PdfCtrl', function($scope, $location) {

  // var absurl = $location.absUrl();
  // var filename = absurl.slice(absurl.indexOf('?file=') + 6 , absurl.length);
  // console.log('-----', urllist);

  $scope.pdf = {
      src: '',
  };
});