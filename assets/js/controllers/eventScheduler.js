app.controller('eventschedulerController', [
    '$scope',
    'hrmAPIservice',
    '$rootScope',
    'cookie',
    '$location',
    'uiGridConstants',
    'moment',
    '$timeout',
    '$filter',
    function ($scope, hrmAPIservice, $rootScope, cookie, $location, uiGridConstants, moment, $timeout, $filter) {
        var userData = cookie.checkLoggedIn();
        cookie.getPermissions();
        $scope.userId = '';
        $scope.isEmployeeListExist = true;
        $scope.employees = [];
        $scope.events = [];
        $scope.selected_emp = [];
        $scope.showMode = 0;
        $scope.detailModal = false;
        $scope.saveModal = false;
        $scope.removeModal = false;
        $scope.limitModal = false;
        $scope.disableEmp = false;
        $scope.emailModal = false;

        //These variables MUST be set as a minimum for the calendar to work
        $scope.calendarView = 'month';
        $scope.viewDate = new Date();
        $scope.alloc_events = [];
        $scope.isCellOpen = false;
        $scope.selected_event = null;
        $scope.index = 0;
        $scope.dateOptions = {
            dateFormat: 'dd/mm/yyyy',
        };
        
        $scope.eventClicked = function(event) {
            hrmAPIservice.getScheduleByID(userData,event.id).then(function(response) {
                $scope.selected_event = response.data.getSchedule[0];
                $scope.selected_event.names = JSON.parse($scope.selected_event.names);
                $scope.selected_event.startsAt = moment(new Date($scope.selected_event.startsAt)).format("DD-MM-YYYY HH:mm");
                $scope.selected_event.endsAt = moment(new Date($scope.selected_event.endsAt)).format("DD-MM-YYYY HH:mm");
                console.log($scope.selected_event);
                $scope.detailModal = true;
            });
        };
        
        $scope.eventEdited = function(event) {
            console.log("event edited");
        };
    
        $scope.eventDeleted = function(event) {
            console.log("event deleted");
        };
        
        $scope.eventTimesChanged = function(event) {
            console.log("event time changed");
        };
    
        $scope.toggle = function($event, field, event) {
            $event.preventDefault();
            $event.stopPropagation();
            event[field] = !event[field];
        };
        $scope.cancel = function() {
            $scope.detailModal = false;
        };
        
        $scope.dayViewEventWidth = 300;
        
        $scope.init = function () {

            $scope.userId = cookie
                .getCookie('user')
                .account_id;
            var perm = cookie.getCookie("permissions");
            if (!perm || perm['52'] == null || perm['52']==undefined) {
                $scope.isAllowed = false;
            } else {
                if (perm['52'].r == '1') {
                    $scope.isAllowed = true;
                } else 
                    $scope.isAllowed = false;
            }
            if(!$scope.isAllowed){
                cookie.deleteCookie('user');
                cookie.deleteCookie('permissions');
                $rootScope.isLoggedin = 0;
                $location.path('/');
            }
            if(!angular.isDefined($rootScope.perms.eventscheduler)){
                $scope.eventscheduler_read = 0;
                $scope.eventscheduler_write = 0;
                $scope.eventscheduler_delete = 0;
            }else{
                $scope.eventscheduler_read = ($rootScope.perms.eventscheduler.read > 0) ? true : false; //eventscheduler permission
                $scope.eventscheduler_write = ($rootScope.perms.eventscheduler.write > 0) ? true : false; //eventscheduler permission
                $scope.eventscheduler_delete = ($rootScope.perms.eventscheduler.delete > 0) ? true : false; //eventscheduler permission
            }
            console.log($scope.limitModal);
            hrmAPIservice.getEvents(userData,0).then(function(response) {
                $scope.events = response.data;
            });
            hrmAPIservice.getEmpList(userData, $scope.showMode).then(function (response) {
                if (response.data.res == null) return;
                $scope.employees = response.data.res;
                getAllocatedEvents();
            });
        }
        $scope.showDataByMode = function(){
            $scope.showMode = !$scope.showMode;
            hrmAPIservice.getEmpList(userData, $scope.showMode).then(function (response) {
                if (response.data.res == null) return;
                $scope.employees = response.data.res;
                getAllocatedEvents();
            });
        }
        $scope.filterAttendeesChange = function(){
            getAllocatedEvents();
        }
        $scope.addEventTemplate = function(){
            $scope.alloc_events.push({id: 0, startsAt_time: new Date('2010-10-10 00:00:00').getTime(), endsAt_time: new Date('2010-10-10 00:00:00').getTime(), draggable: true, resizable: true})
        }
        $scope.saveAllocEvent = function(index){
            console.log($scope.alloc_events[index].user_id);
            if($scope.alloc_events[index].user_id!=undefined && $scope.alloc_events[index].class_limit<$scope.alloc_events[index].user_id.length){
                $scope.limitModal = true;
            }
            else{
                $scope.saveModal = true;
                $scope.index = index;
            }
        }
        $scope.save = function(){
            //console.log($scope.alloc_events[$scope.index]);
            if(typeof $scope.alloc_events[$scope.index].startsAt_date === 'string'){
                var dateString = $scope.alloc_events[$scope.index].startsAt_date; // Oct 23
                var dateParts = dateString.split("-");
                // month is 0-based, that's why we need dataParts[1] - 1
                $scope.alloc_events[$scope.index].startsAt_date = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]); 
            }
            if(typeof $scope.alloc_events[$scope.index].endsAt_date === 'string'){
                var dateString = $scope.alloc_events[$scope.index].endsAt_date; // Oct 23
                var dateParts = dateString.split("-");
                // month is 0-based, that's why we need dataParts[1] - 1
                $scope.alloc_events[$scope.index].endsAt_date = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]); 
            }
            console.log($scope.alloc_events[$scope.index].startsAt_date);
            console.log($scope.alloc_events[$scope.index].endsAt_date);
            var one = {
                id: $scope.alloc_events[$scope.index].id,
                event_id: $scope.alloc_events[$scope.index].event_id,
                class_limit: $scope.alloc_events[$scope.index].class_limit,
                user_id: $scope.alloc_events[$scope.index].user_id,
                startsAt_date: $scope.alloc_events[$scope.index].startsAt_date.toDateString(),
                startsAt_time: moment(new Date($scope.alloc_events[$scope.index].startsAt_time)).format('HH:mm'),
                endsAt_date: $scope.alloc_events[$scope.index].endsAt_date.toDateString(),
                endsAt_time: moment(new Date($scope.alloc_events[$scope.index].endsAt_time)).format('HH:mm'),
                alloc_date: moment(new Date()).format('YYYY-MM-DD HH:mm:ss')
            };
            console.log(one);
            hrmAPIservice.saveAllocatedEvent(userData, one).then(function(response) {
                getAllocatedEvents();
                $scope.saveModal = false;
                //window.location.reload();
            });
        }
        $scope.cancelSave = function(){
            $scope.saveModal = false;
        }
        $scope.removeAllocEvent = function(index){
            $scope.removeModal = true;
            $scope.index = index;
        }
        $scope.remove = function(){
            var del_id = $scope.alloc_events[$scope.index].id;
            console.log(del_id);
            if(del_id==0) $scope.alloc_events.splice($scope.index, 1);
            else{
                hrmAPIservice.deleteAllocatedEvent(userData, del_id).then(function(response) {
                    //$scope.alloc_events = response.data.alloc_events;
                    getAllocatedEvents();
                    $scope.removeModal = false;
                    //window.location.reload();
                });
            }
        }
        $scope.cancelRemove = function(){
            $scope.removeModal = false;
        }
        $scope.agreeLimit = function(){
            
            $scope.limitModal = false;
        }
        $scope.emailAllocEvent = function(index){
            $scope.emailModal = true;
            console.log(index);
            
            $scope.index = index;
            $scope.cur_emps = [];
            var cur_emps = $scope.alloc_events[$scope.index].user_id;
            var cnt = 0;
            for(var i=0; i<$scope.employees.length; i++)
                for(var j=0; j<cur_emps.length; j++){
                    if($scope.employees[i].id==cur_emps[j])
                        $scope.cur_emps[cnt++] = $scope.employees[i];
                }
            console.log($scope.cur_emps);
        }
        $scope.send_email = function(){
            
            $(".alertMessage").addClass('hide');
            var event_id = $scope.alloc_events[$scope.index].id;
            var email_attendees = $scope.sel_emps;
            console.log(email_attendees);
            hrmAPIservice.sendEmailToAttendants(userData, event_id, email_attendees).then(function(response) {
                console.log(response.data);
                $(".alertMessage").removeClass('hide');
                //$scope.emailModal = false;
            });
        }
        $scope.cancelEmail = function(){
            $scope.emailModal = false;
            
            $(".alertMessage").addClass('hide');
        }
        $scope.dirtyFix= function () {
            console.log('remove style attr');
            $timeout(function () {
                $(".md-scroll-mask").css("z-index", 899);
                $(".md-scroll-mask").click(function () {
                    $(".md-select-menu-container").remove();
                    $(".md-scroll-mask").css("z-index", -1);
                });
                
                $("body").removeAttr("style"); 
            }, 500);
        }
        function getAllocatedEvents(){
            hrmAPIservice.getAllocatedEvents(userData, $scope.selected_emp, $scope.showMode).then(function(response) {
                var alloc_events = [];
                $scope.alloc_events = response.data.alloc_events;
                if($scope.alloc_events.length>0){
                    for(var i=0; i<$scope.alloc_events.length; i++)
                    {
                        var ee = $scope.alloc_events[i];
                        ee.user_id = JSON.parse(ee.user_id);
                        ee.startsAt_date = $filter('date')(new Date(ee.startsAt),'dd-MM-yyyy');
                        ee.startsAt_time = new Date(ee.startsAt).getTime();
                        ee.endsAt_date = $filter('date')(new Date(ee.endsAt),'dd-MM-yyyy');
                        ee.endsAt_time = new Date(ee.endsAt).getTime();
                        ee.class_limit = ee.class_limit;
                        console.log(ee.user_id);
                    }
                }
            });
        }
    }
]);
$(document).click(function(e) { 
    // Check for left button
    var dropdown = $(".md-select-menu-container.md-active.md-clickable")
    if(e.target!=dropdown)
        $(dropdown).hide();
});