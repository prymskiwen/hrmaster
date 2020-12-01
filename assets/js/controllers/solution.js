app.controller('solutionController', ['$scope', '$rootScope', 'cookie','$anchorScroll','$routeParams', function ($scope, $rootScope, cookie, $anchorScroll, $routeParams) {
    var userData = cookie.checkLoggedIn();
    $rootScope.showHeader = 0;
    $scope.isLoggedin = 0;
}]);
