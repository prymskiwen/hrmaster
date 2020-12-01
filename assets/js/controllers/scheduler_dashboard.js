app.controller("schedulerdashboardController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", '$location', function($scope, $rootScope, cookie, hrmAPIservice, $location) {
   
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['56'] == null || perm['56']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['56'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    $scope.pageTitle = "Event Scheduler Dashboard";
    $scope.courses = [];
    $scope.showModal = false;
    var showMode = 0;
    var locationMode = 0;
    $scope.showMode = 0;
    $scope.locationMode = 0;
    
    if(!angular.isDefined($rootScope.perms.schedulerdashboard)){
        $scope.schedulerdashboard_read = 0;
        $scope.schedulerdashboard_write = 0;
        $scope.schedulerdashboard_delete = 0;
    }else{
        $scope.schedulerdashboard_read = ($rootScope.perms.schedulerdashboard.read > 0) ? true : false; //tranning permission
        $scope.schedulerdashboard_write = ($rootScope.perms.schedulerdashboard.write > 0) ? true : false; //tranning permission
        $scope.schedulerdashboard_delete = ($rootScope.perms.schedulerdashboard.delete > 0) ? true : false; //tranning permission
    }

    hrmAPIservice.getSchedulerByUser(userData, 0, 0).then(function(response) {
        console.log(response.data);
       $scope.gridOptionsComplex.data = response.data;
    });
    $scope.updateFilterStatus = function(status){
        showMode=status;
        $scope.showMode=status;
        hrmAPIservice.getSchedulerByUser(userData, $scope.showMode, $scope.locationMode).then(function(response) {
            console.log(response.data);
           $scope.gridOptionsComplex.data = response.data;
        });
    }
    $scope.updateFilterLocation = function(location){
        locationMode=location;
        $scope.locationMode=location;
        hrmAPIservice.getSchedulerByUser(userData, $scope.showMode, $scope.locationMode).then(function(response) {
            console.log(response.data);
           $scope.gridOptionsComplex.data = response.data;
        });
    }
    $scope.gridOptionsComplex = {
      enableFiltering: true,
      showGridFooter: false,
      showColumnFooter: false,
      onRegisterApi: function onRegisterApi(registeredApi) {
          gridApi = registeredApi;
      },
      columnDefs: [
        { name: 'id', visible: false },
        { name: 'Name', width: '15%',menableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'EventName', width: '30%', enableFiltering: true, cellClass: 'center', enableCellEdit: false},
        { name: 'SiteLocation', width: '15%', enableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'Department', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'DateOfCourse', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'Status', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
            if (grid.getCellValue(row ,col)=="Attended") {
              return 'green';
            } else if(grid.getCellValue(row ,col)=="Not attended") {return 'red';}
            else {return 'blue';}
          }
        },
        { name: 'DaysUntilCourse', enableCellEdit: false, cellClass: 'center', enableFiltering: true}
      ]
    };
}]);