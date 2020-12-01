app.controller('employee_dashboardController', ['$scope', 'hrmAPIservice', '$rootScope', 'cookie', function ($scope, hrmAPIservice, $rootScope, cookie) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    $scope.txt = '';
    $scope.init = function(){
    
    console.log("AAAAAAAAAAAAAAAAAAAA");
    var perm = cookie.getCookie("permissions");
    // console.log('pppp',perm['25'].r);
    if (!perm || perm['25'] == null || perm['25']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['25'].r == '1') $scope.isAllowed = true;
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