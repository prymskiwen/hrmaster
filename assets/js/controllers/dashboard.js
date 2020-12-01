app.controller('dashboardController', ['$scope', 'hrmAPIservice', '$rootScope', 'cookie', '$location', function ($scope, hrmAPIservice, $rootScope, cookie, $location) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    $scope.isAllowed = false;
    $scope.init = function(){
        var perm = cookie.getCookie("permissions");
        if (!perm || perm['1'] == null || perm['1']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['1'].r == '1') $scope.isAllowed = true;
            else                     $scope.isAllowed = false;
        }
        if(!$scope.isAllowed){
            cookie.deleteCookie('user');
            cookie.deleteCookie('permissions');
            $rootScope.isLoggedin = 0;
            $location.path('/');
        }
        hrmAPIservice.getRoles().then(function(response) {
            $scope.roles = response.data;
            // console.log('test',userData);
            for(var i = 0;i<$scope.roles.length;i++){
                if($scope.roles[i].id == userData.usertype_id){
                    $location.path('dashboard/'+$scope.roles[i].value.toLowerCase());
                }
            }
            
          });
    }
}]);

