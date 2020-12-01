app.controller('managerController', ['$scope', 'hrmAPIservice', '$rootScope', 'cookie', '$location', function ($scope, hrmAPIservice, $rootScope, cookie, $location) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    $scope.txt = '';
    $scope.isAllowed = false;
    
    $scope.init = function(){
        var perm = cookie.getCookie("permissions");
        console.log(perm);
        console.log($rootScope.perms);
        if (!perm || perm['23'] == null || perm['23']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['23'].r == '1') $scope.isAllowed = true;
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