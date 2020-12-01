app.controller('createeventController', ['$scope', '$rootScope', 'cookie', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, $location, hrmAPIservice) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['53'] == null || perm['53']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['53'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    $scope.event = {};
    $scope.showMessage = 0;
    $scope.formEnabled = 0;
    $scope.clear = {};
    
    $scope.newEvent = function() {
        //alert("a");
        $scope.eventform.$setPristine();
        $scope.event.account_id = userData.account_id;
        $scope.event.created_by = userData.id;
        $scope.event.update_by = 0;
        $scope.showMessage = 0;
        $scope.formEnabled = 1;
    }  
    
    $scope.editEvent = function(event) { 
        $scope.showMessage = 0;
        hrmAPIservice.getEventById(event.id).then(function(response) {     
            $scope.event = response.data.event;
            $scope.formEnabled = 1;
        });
    };    
    $scope.saveEvent = function() {
        hrmAPIservice.saveEvent($scope.event, userData).then(function(response) {
            console.log("Save event: ", response);

            $scope.showMessage = 1;
            $scope.gridOptionsComplex.data = response.data.events;
            $scope.success = response.data.success;
            $scope.eventMessage = response.data.message;
            $scope.formEnabled = 0;
            $scope.event = {};
        });
    };
    $scope.clearForm = function() {
        $scope.event = angular.copy($scope.clear);
        //$scope.event_form.$valid = false;
    } 
    $scope.deleteEvent = function(eventDetail) {
        console.log(eventDetail);
        var answer = confirm("Delete event '" + eventDetail.event_name + "'? Are you sure?");
        if (answer) {
            hrmAPIservice.delete(eventDetail, userData, 'event').then(function(response) {
                $scope.gridOptionsComplex.data = response.data;
            });
        }
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
        { name: 'event_name', enableFiltering: true, enableCellEdit: false,},
        { name: 'event_type', enableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'venue_name', enableFiltering: true, cellClass: 'center', enableCellEdit: false,},
        { name: 'suburb', enableFiltering: true, cellClass: 'center', enableCellEdit: false,},
        { name: 'action', enableFiltering: false, cellClass: 'center', enableCellEdit: false, 
            cellTemplate: '<div class="ui-grid-cell-contents"><i class="fa fa-edit fa-2x" ng-click="grid.appScope.editEvent(row.entity)"></i><i class="fa fa-trash-o fa-2x text-danger" ng-click="grid.appScope.deleteEvent(row.entity)"></i></div>'
        }
      ]
    };    
    
    hrmAPIservice.getEvents(userData,0).then(function(response) {
        $scope.gridOptionsComplex.data = response.data;
    });
    hrmAPIservice.getStates().then(function(response) {
        var states = [];
        var count = 0;
        for(var i = 0; i < response.data.length; i++) {
            var item = response.data[i];
            states[count++] = item;
        }
        $scope.states = states;
    });
    hrmAPIservice.getEventTypes().then(function(response) {
        var types = [];
        var count = 0;
        for(var i = 0; i < response.data.length; i++) {
            var type = response.data[i];
            types[count++] = type;
        }
        $scope.eventtypes = types;
    });
    hrmAPIservice.getEventNames(userData).then(function(response) {
        $scope.license_name_list = response.data.event_names;
    });
}]);
