app.controller('administratorController', ['$scope', 'hrmAPIservice', '$rootScope', 'cookie', '$location', function ($scope, hrmAPIservice, $rootScope, cookie, $location) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    $scope.txt = '';
    $scope.isAllowed = false;
    $scope.init = function(){
        var perm = cookie.getCookie("permissions");
        console.log(perm);
        if (!perm || perm['22'] == null || perm['22']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['22'].r == '1') $scope.isAllowed = true;
            else                     $scope.isAllowed = false;
        }
        if(!$scope.isAllowed){
            cookie.deleteCookie('user');
            cookie.deleteCookie('permissions');
            $rootScope.isLoggedin = 0;
            $location.path('/');
        }
    }
}]);