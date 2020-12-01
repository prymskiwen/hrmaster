app.controller("trainingcourselistController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", '$location', function($scope, $rootScope, cookie, hrmAPIservice, $location) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();

    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['18'] == null || perm['18']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['18'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }

    $scope.pageTitle = "Training Courses";
    $scope.courses = [];
    $scope.showModal = false;

    hrmAPIservice.getCoursesByUser(userData.id).then(function(response) {
        $scope.gridOptionsComplex.data = response.data;
    });

    $scope.gridOptionsComplex = {
      enableFiltering: true,
      showGridFooter: false,
      showColumnFooter: false,
      onRegisterApi: function onRegisterApi(registeredApi) {
          gridApi = registeredApi;
      },
      columnDefs: [
        { name: 'id', visible: false },
        { name: 'course', width: '30%', enableCellEdit: false },
        { name: 'CourseStatus', width: '10%', cellClass: 'center', enableCellEdit: false,
            cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                if (grid.getCellValue(row,col) == 'Pending') {
                    return 'center pending';
                } else if (grid.getCellValue(row,col) == 'Overdue') {
                    return 'center expired';
                }else if (grid.getCellValue(row,col) == 'Fail'){
                    return 'center failed';
                }else if (grid.getCellValue(row,col) == 'Incomplete'){
                    return 'center incompleted';
                }
                else {
                    return 'center completed';
                }
            },
            // updated by Alex-cobra -20-04-14
            cellTemplate : '<div ng-hide="row.entity.CourseStatus !== \'Pending\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="blue">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Completed\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="green">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Overdue\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="red">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Incomplete\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="yellow">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Submitted\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="purple">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Fail\'"><a href="javascript:void(0);" ng-click="grid.appScope.gotoCourse(row.entity,1)"><span class="red">{{ grid.getCellValue(row, col) }}</span></a></div>\n\
                            <div ng-hide="row.entity.CourseStatus !== \'Expired\'"><span class="brown">{{ grid.getCellValue(row, col) }}</span></div>'
            // // // // //
        },
        { name: 'DateStarted', width: '15%', enableFiltering: true, cellClass: 'center', enableCellEdit: false },
        { name: 'DateCompleted', width: '15%', cellClass: 'center', enableCellEdit: false },
        { name: 'type', width: '10%', enableFiltering: true, cellClass: 'center', enableCellEdit: false, displayName: 'Type' },
        { name: 'TimeLeft', width: '10%', enableCellEdit: false,  enableFiltering: true,
            cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                if (grid.getCellValue(row,col) < 0) {
                    return 'center expired';
                } else {
                    return 'center';
                }
            }
        },
        //start updated by Alex-cobra-2020-04-17(add view row to table)
        { name: 'Action', width: '10%', cellClass: 'center', enableCellEdit: false,
            cellTemplate : '<span><a href="javascript:void(0);" ng-click="grid.appScope.course_result_details(row.entity,1)">view</a></span>'
        }
        //end updated by Alex-cobra-2020-04-17
      ]
    };

    $scope.gotoCourse = function(courseObj) {
        console.log(courseObj);
        if (courseObj.CourseStatus == 'Completed') {
            return;
        }
        if(courseObj.CourseStatus == 'Overdue' && courseObj.is_locked == 1 && courseObj.extra_hours==0){
            return;
        }
        $location.path('/docourse/' + courseObj.course_id + '/' + courseObj.employee_id);
    };
    //start updated by Alex-cobra-2020-04-17(custom table contents)
        $scope.course_result_details = function (courseObj) {
            $scope.course = {};
            $scope.course_result_detail = "";
            var content = "<table class='table table-bordered table-responsive'>" +
                "<thead>" +
                "<tr>" +
                "<th colspan='4' class='center' '>"+courseObj.course+" ("+courseObj.type+")</th>" +
                "</th>" +
                "</thead>" +
                "<tbody>" +
                "<tr class='center'>" +
                "<td>Question</td>" +
                "<td>Chosen Answer</td>" +
                "<td>Date and Time in AU format</td>" +
                "<td>IP Address Entered</td>" +
                "</tr>";

            hrmAPIservice.getEmployeeCourseLastAttempt(courseObj.course_id, courseObj.employee_id).then(function(response) {

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
                        "</tr>"
                }

                content += "</tbody>" +
                    "</table>";
                $scope.course_result_detail = content
            });


        }
    //end updated by Alex-cobra-2020-04-17
}]);
