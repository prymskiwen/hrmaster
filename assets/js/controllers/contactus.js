app.controller('contactusController', ['$scope', '$rootScope', 'cookie', function ($scope, $rootScope, cookie) {

    var userData = cookie.checkLoggedIn();
    $rootScope.showHeader = 0;
    $scope.isLoggedin = 0;
}]);
