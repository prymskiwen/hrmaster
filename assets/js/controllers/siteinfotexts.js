app.controller('siteinfotextsController', ['$scope', '$rootScope', 'cookie','uiGridConstants', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, uiGridConstants, $location, hrmAPIservice) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();

    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    console.log(perm);
    if (!perm || perm['55'] == null || perm['55']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['55'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    $scope.pageTitle = "Website Information Icon's Text";
    $scope.formEnabled = 0;
    $scope.it = {};
    $scope.new_type = "";
    $scope.showMessage = 0;

    $scope.gridOptionsComplex = {
      enableFiltering: true,
      showGridFooter: false,
      showColumnFooter: false,
      onRegisterApi: function onRegisterApi(registeredApi) {
          gridApi = registeredApi;
      },
      columnDefs: [
        { name: 'id', visible: false },
        { name: 'type', width: '20%' },
        { name: 'value', width: '20%' },
        { name: 'text', width: '40%', cellClass: 'center' },
        { name: 'action', enableFiltering: false, width: '20%',  cellClass: 'center',
            cellTemplate: '<div class="ui-grid-cell-contents"><i class="fa fa-edit fa-2x" ng-click="grid.appScope.editInfoText(row.entity)"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-trash-o fa-2x text-danger" ng-click="grid.appScope.deleteInfoText(row.entity)"></i></div>'
        }
      ]
    };

    $scope.deleteInfoText = function(itDetail) {
        var answer = confirm("Delete " + itDetail.text + '? Are you sure?');
        if (answer) {
            hrmAPIservice.deleteInfoText(itDetail.id, userData).then(function(response) {
                $scope.getInfoTexts();
            });
        }
    }


    $scope.newInfoText = function() {
        $scope.sdform.$setPristine();
        $scope.it.id = 0;
        $scope.it.type="";
        $scope.it.value="";
        $scope.it.text="";
        $scope.showMessage = 0;
        $scope.formEnabled = 1;
    }

    $scope.clearForm = function() {
        $scope.it = {};
        $scope.it.id = 0;
        $scope.formEnabled = 0;
    }

    var setDate = function(date) {
        var a = date.split('-');
        var d = new Date(a[0], a[1]-1, a[2]);
        return d;
    }
    
    $scope.onInfoTypeChanged = function(){
        console.log($scope.it.type);
    }

    $scope.editInfoText = function(itDetail) {
        $scope.showMessage = 0;
        hrmAPIservice.getInfoTextById(itDetail.id).then(function(response) {
            console.log(response.data);
            $scope.it = response.data;
            $scope.formEnabled = 1;
        });
    };

    $scope.getInfoTexts = function() {
        hrmAPIservice.getInfoTexts(userData, 0).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.info_texts;
            $scope.typeList = response.data.datatype;        
        });
    }

    $scope.saveInfoText = function() {
        userData.account_id=0;
        $scope.showMessage = 0;
        if($scope.it.type=="other") $scope.it.type=$scope.new_type;
        hrmAPIservice.saveInfoText($scope.it, userData).then(function(response) {
            console.log(response);
            $scope.getInfoTexts();
            if(response.data.message){
                $scope.success = 0;
                $scope.notificationMessage = response.data.message;
            }else{
                $scope.success = 1;
                if($scope.it.id==0)
                    $scope.notificationMessage = "New information text is successfully saved.";
                else $scope.notificationMessage = "Message has been successfully modified";
            }
            $scope.it = {};
            $scope.new_type="";
            $scope.formEnabled = 0;
            $scope.showMessage = 1;
        });
    }
    
    $scope.getInfoTexts();

}]);
