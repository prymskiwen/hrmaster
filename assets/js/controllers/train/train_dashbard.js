app.controller("TrainDashboardController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", "$location", "$routeParams",  function($scope, $rootScope, cookie, hrmAPIservice, $location, $routeParams) {
    var userData = cookie.checkLoggedIn(), courseCategory = [];
    cookie.getPermissions();
    
    console.log(new Date().getTime());

    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['28'] == null || perm['28'] == undefined)      $scope.isAllowed = false;
    else {
        if (perm['28'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }

    var showMode = 0;
    var ownMode = 0;
    var locationMode = 0;

    $scope.pageTitle = "Dashboard";
    $scope.alloc_courses = [];
    $scope.showModal = false;
    $scope.showMode = 0;
    $scope.ownMode = 0;
    $scope.locationMode = 0;
    if(!angular.isDefined($rootScope.perms.TrainDashboard)){
        $scope.training_read = 0;
        $scope.training_write = 0;
        $scope.training_delete = 0;
    }else{
        $scope.training_read = ($rootScope.perms.TrainDashboard.read > 0) ? true : false; //tranning permission
        $scope.training_write = ($rootScope.perms.TrainDashboard.write > 0) ? true : false; //tranning permission
        $scope.training_delete = ($rootScope.perms.TrainDashboard.delete > 0) ? true : false; //tranning permission
    }
    // Sort function.
    $scope.sort = function(keyname){
        $scope.sortKey = keyname;   //set the sortKey to the param passed
        $scope.reverse = !$scope.reverse; //if true make it false and vice versa
    };

    $scope.showActiveData = function(){
        $scope.showMode = 0;
        showMode = $scope.showMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            //var alloc_courses = response.data.alloc_courses;
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
            //filterAllocCourseData(alloc_courses);
        });
    }

    $scope.showAllData = function(){
        $scope.showMode = 1;
        showMode = $scope.showMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            //var alloc_courses = response.data.alloc_courses;
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
            //filterAllocCourseData(alloc_courses);
        });
    }

    $scope.showOwnTrainingData = function(){
        $scope.ownMode = 0;
        ownMode = $scope.ownMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            //var alloc_courses = response.data.alloc_courses;
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
            //filterAllocCourseData(alloc_courses);
        });
    }

    $scope.showAllTrainingData = function(){
        $scope.ownMode = 1;
        ownMode = $scope.ownMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
        });
    }

    $scope.showYourLocationData = function(){
        $scope.locationMode = 0;
        locationMode = $scope.locationMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            //var alloc_courses = response.data.alloc_courses;
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
            //filterAllocCourseData(alloc_courses);
        });
    }

    $scope.showAllLocationsData = function(){
        $scope.locationMode = 1;
        locationMode = $scope.locationMode;
        hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.alloc_courses;
        });
    }
    console.log("start");
    hrmAPIservice.getAllocCourseData(userData, showMode, ownMode, locationMode).then(function(response) {
        $scope.gridOptionsComplex.data = response.data.alloc_courses;
    });

//start updated by Alex-cobra-2020-04-17(add view row to table)
    $scope.gridOptionsComplex = {
        enableFiltering: true,
        showGridFooter: true,
        showColumnFooter: false,
        onRegisterApi: function onRegisterApi(registeredApi) {
            gridApi = registeredApi;
        },
        columnDefs: [
            { name: 'id', visible: false },
            { name: 'PersonName', width: '15%', enableCellEdit: false, displayName: 'Person Name' },
            { name: 'course_name', width: '25%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, displayName: 'Course Name' },
            { name: 'SiteLocation', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, displayName: 'Site Location' },
            { name: 'AllocDate', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, displayName: 'Date Allocated' },
            { name: 'course_status', width: '10%', enableFiltering: true, cellClass: 'center', displayName: 'Status', headerCellClass: 'center', enableCellEdit: false,
                cellTemplate: '<div ng-hide="row.entity.course_status !== \'Pending\'"><span class="blue">Pending</span></div>\n\
                                <div ng-hide="row.entity.course_status != \'Completed\'"><span class="green">Completed</span></div>\n\
                                <div ng-hide="row.entity.course_status != \'Overdue\'"><span class="red">Overdue</span></div>\n\
                                <div ng-hide="row.entity.course_status != \'Incomplete\'"><span class="yellow">Incomplete</span></div>\n\
                                <div ng-hide="row.entity.course_status != \'Submitted\'"><span class="purple">Submitted</span></div>\n\
                                <div ng-hide="row.entity.course_status != \'Expired\'"><span class="brown">Expired</span></div>'
                },
            { name: 'type', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, displayName: 'Type' },
            { name: 'TimeLeft', width: '10%', enableFiltering: false, cellClass: 'center', headerCellClass: 'center', enableCellEdit: false, displayName: 'Days Remaining' },
            { name: 'action', enableFiltering: false, width: '10%',  cellClass: 'center', enableSorting: false,headerCellClass: 'center', enableCellEdit: false,
                cellTemplate: '<div class="ui-grid-cell-contents"><i class="fa fa-eye fa-2x" ng-click="grid.appScope.course_result_details(row.entity)"></i>&nbsp;&nbsp;<i class="fa fa-edit fa-2x" ng-click="grid.appScope.editAllocCourse(row.entity)" ng-if="grid.appScope.training_write"></i>&nbsp;&nbsp;<i class="fa fa-trash-o fa-2x text-danger" ng-click="grid.appScope.removeAllocCourse(row.entity)" ng-if="grid.appScope.training_delete"></i></div>'},
        ]
    };
//end updated by Alex-cobra-2020-04-17

    function filterAllocCourseData(alloc_courses) {
        var courses = [];
        for(var i = 0; i < alloc_courses.length; i++) {
            var item = alloc_courses[i];
            var status = item.status;

            //Completed.
            if(status == 1) {
                item["course_status"] = "Completed";
                item["status_class"] = "green";
                var completed_date = new Date(item["completed_date"]);
                var day = completed_date.getDate()
                var month = completed_date.getMonth() + 1
                var year = completed_date.getFullYear()
                var completed_string = day + "/" + month + "/" + year;

                item["days_remain"] = completed_string;
            }
            else if(status == 0) {
                var alloc_date = item["alloc_date"];
                var expire_hours = item["expire_hours"];

                var date1 = new Date(alloc_date);
                var date1 = dateAdd(date1, expire_hours, 'hours');
                var date2 = new Date();

                var timeDiff = date1.getTime() - date2.getTime();
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

                item["days_remain"] = diffDays;

                if(diffDays >= 0) {
                    item["course_status"] = "Pending";
                    item["status_class"] = "blue";
                }
                else {
                    item["course_status"] = "Overdue";
                    item["status_class"] = "red";
                }

            }
            courses[i] = item;
        }
        $scope.alloc_courses = courses;
    }

    $scope.allocCourse = function() {
        location.href = "#/alloc_course";
    }

    function dateAdd(original, increment, unit) {

        // Return undefiend if first argument isn't a Date object
        if (!(original instanceof Date)) {
            return(undefined);
        }

        switch(unit) {
            case 'seconds':
                // Add number of secodns to current date (ms*1000)
                var newDate = new Date(original);
                newDate.setTime(original.getTime() + (increment*1000));
                return newDate;
                break;
            case 'minutes':
                // Add number of minutes to current date (ms*1000*60)
                var newDate = new Date(original);
                newDate.setTime(original.getTime() + (increment*1000*60));
                return newDate;
                break;
            case 'hours':
                // Add number of hours to current date (ms*1000*60*60)
                var newDate = new Date(original);
                newDate.setTime(original.getTime() + (increment*1000*60*60));
                return newDate;
                break;
            case 'days':
                // Add number of days to current date
                var newDate = new Date(original);
                newDate.setDate(original.getDate() + increment);
                return newDate;
                break;
            case 'weeks':
                // Add number of weeks to current date
                var newDate = new Date(original);
                newDate.setDate(original.getDate() + (increment*7));
                return newDate;
                break;
            case 'months':
                // Get current date
                var oldDate = original.getDate();

                // Increment months (handles year rollover)
                var newDate = new Date(original);
                newDate.setMonth(original.getMonth() + increment);

                // If new day and old day aren't equal, set new day to last day of last month
                // (handles edge case when adding month to Jan 31st for example. Now goes to Feb 28th)
                if (newDate.getDate() != oldDate) {
                    newDate.setDate(0);
                }

                // Handle leap years
                // If old date was Feb 29 (leap year) and new year isn't leap year, set new date to Feb 28
                if (original.getDate() == 29 && !isLeapYear(newDate.getFullYear())) {
                    newDate.setMonth(1);
                    newDate.setDate(28);
                }

                return newDate;
                break;
            case 'years':
                // Increment years
                var newDate = new Date(original);
                newDate.setFullYear(original.getFullYear() + increment);

                // Handle leap years
                // If old date was Feb 29 (leap year) and new year isn't leap year, set new date to Feb 28
                if (original.getDate() == 29 && !isLeapYear(newDate.getFullYear())) {
                    newDate.setMonth(1);
                    newDate.setDate(28);
                }

                return newDate;
                break;
            // Defaults to milliseconds
            default:
                var newDate = new Date(original);
                newDate.setTime(original.getTime() + increment);
                return newDate;
        }
    };

    function isLeapYear(year) {
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
    }

    // Remove Course.
    $scope.removeAllocCourse = function(course) {
        $scope.showModal = true;
        selectedCourse = course;
    };

    $scope.remove = function() {
        $scope.showModal = false;
        if(selectedCourse != null) {
            hrmAPIservice.delAllocCourse(selectedCourse.id, userData.id).then(function(response) {
                var alloc_courses = response.data.alloc_courses;
                filterAllocCourseData(alloc_courses);
            });
        }
    };

    $scope.cancel = function() {
        $scope.showModal = false;
    };

    // updated by Alex-cobra -20-04-07
    $scope.editAllocCourse = function(course) {
        hrmAPIservice.getCourseByAllocId(course.id).then(function (response) {
            if (response.data.course_type == 'Questions And Answers' && course.course_status == 'Submitted' ) {
                location.href = "#/mark_course/" + course.id;
            }
            else {
                location.href = "#/edit_alloc_course/" + course.id;

            }
        });
    };
    // // // // //

    //start updated by Alex-cobra-2020-04-17(custom table contents)
    $scope.course_result_details = function (courseObj) {
        console.log(courseObj.UID);
        
        $scope.course = {};
        $scope.course_result_detail = "";
        var content = "<table class='table table-bordered table-responsive'>" +
            "<thead>" +
            "<tr>" +
            "<th colspan='4' class='center' '>"+courseObj.course_name+" ("+courseOjb.type+"</th>" +
            "</th>" +
            "</thead>" +
            "<tbody>" +
            "<tr class='center'>" +
            "<td>Question</td>" +
            "<td>Chosen Answer</td>" +
            "<td>Date and Time in AU format</td>" +
            "<td>IP Address Entered</td>" +
            "</tr>";

        hrmAPIservice.getEmployeeCourseLastAttempt(courseObj.course_id, courseObj.UID).then(function(response) {

            $scope.course.questions = response.data.course.questions;
            console.log(response)

            for (var i = 0; i < $scope.course.questions.length; i ++){
                let datetime = $scope.course.questions[i]["answers"][0]["date_submitted"];
                console.log(datetime);
                let date, time;
                if(datetime){
                    date = datetime.split(" ")[0];
                    time = datetime.split(" ")[1];
                    date = date.split("-").reverse().join("-");
                }
                console.log("ip: "+$scope.course.questions[i]["answers"][0]["ip_address"]);
                content += "<tr>" +
                    "<td width='38%'>" + $scope.course.questions[i]["title"] + "</td>" +
                    "<td width='38%'>" + $scope.course.questions[i]["answers"][0]["title"] + "</td>" +
                    "<td width='12%'>" + (datetime ? (date + " " + time) : "undefined") + "</td>" +
                    "<td width='12%'>" + $scope.course.questions[i]["answers"][0]["ip_address"] + "</td>" +
                    "</tr>";
            }

            content += "</tbody>" +
                "</table>";
            $scope.course_result_detail = content
        });


    }
    //end updated by Alex-cobra-2020-04-17
}]);

