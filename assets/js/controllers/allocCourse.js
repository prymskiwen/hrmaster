app.controller("AllocCourseController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", "$location",  function($scope, $rootScope, cookie, hrmAPIservice, $location) {
    var userData = cookie.checkLoggedIn(), courseCategory = [];
    cookie.getPermissions();

    // list of `state` value/display objects
    $scope.cs = {};
    //$scope.ctrl.states        = loadAll();
    $scope.cs.selectedItem  = null;
    $scope.cs.searchText    = null;
    $scope.pageTitle = "";
    $scope.course_keyword = "";
    $scope.alloc_course_master = {
        course_id: '',
        course_supervisor: '',
        course_description: '',
        employee_id: '',
        alloc_date: '',
        expire_hours: 0,
        is_sending_email: true,
        daily_reminder_email_receiver: null,
        status: 0,
        user_id: userData.id,
    };
    
    var perm = cookie.getCookie("permissions");
    console.log(perm);
    if (!perm || perm['27'] == null || perm['27']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['27'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    const clearUserMesage = function(){
        $scope.showMessage = 0;
        $scope.success = 0;
        $scope.userMessage = '';                  
    }    
    
    $scope.alloc_course = angular.copy($scope.alloc_course_master);
    
    $scope.course_supervisor = '';

    $scope.show_expire_date_field = false;
    $scope.alloc_date_type = 'now';

    // $scope.course_name_list = [];
    $scope.course_list = [];
    $scope.course = {};

    $scope.user_name_list = [];
    $scope.user_list = [];
    $scope.employee_list = [];
    $scope.allusers = [];
    $scope.employee = {};

    // Get Course List.
    hrmAPIservice.getCourseData(userData.id, true).then(function(response) {
        //var courses = response.data.courses;
        
        var courses = response.data.courses.filter(function(course) {
            return course.status == 1;
        });        

        $scope.course_list = courses;
        
        hrmAPIservice.getInfoTextByType(userData, "alloc_course").then(function(response){
            console.log(response);
            $scope.info_course = response.data.result.course;
            $scope.info_course_supervisor = response.data.result.course_supervisor;
            $scope.info_course_description = response.data.result.course_description;
            $scope.info_employee = response.data.result.employee;
            $scope.info_alloc_date = response.data.result.alloc_date;
            $scope.info_course_expire_hours = response.data.result.course_expire_hours;
            $scope.info_send_email = response.data.result.send_email;
            $scope.info_course_reminder = response.data.result.course_reminder;
        });
        
        hrmAPIservice.getActiveUsers(userData, 0).then(function(response) {
            //$scope.allusers = response.data;
            $scope.employee_list = response.data;
            console.log($scope.employee_list);
            $scope.allusers = response.data.map(function(usr) {
                return {
                    value: usr.id,
                    display: usr.firstname + " " + usr.lastname
                };
            });
        });
    });
    
    $scope.selectEmployee = function() { 
        if($scope.cs.employee_name)
            $scope.alloc_course.employee_id = $scope.cs.employee_name.value;
    }      
    
    $scope.selectSupervisor = function() { 
        if($scope.cs.course_supervisor)
            $scope.alloc_course.course_supervisor = $scope.cs.course_supervisor.value;
    }      
    
    $scope.cs.querySearch = function(query) { 
        if(query != null && query.length > 0) {
            return $scope.allusers.filter(function(user) {
               return user.display.toLowerCase().indexOf(query.toLowerCase()) > -1;
            });                        
        }
    }
    
    $scope.cs.employeeSearch = function(query) {
        if(query != null && query.length > 0) {
            return $scope.allusers.filter(function(user) {
               return user.display.toLowerCase().indexOf(query.toLowerCase()) > -1;
            });                        
        }
    }   
    
    $scope.updateDescription = function() {
        $scope.alloc_course.course_description  = '';
        
        angular.forEach($scope.course_list, function(node) {          
            if ($scope.alloc_course.course_id == node.course_id) {
                $scope.alloc_course.course_description = node.course_description;
            }
        });        
    }
    
    $scope.expire_hours = {
        availableOptions: [
            {"value": '24', "title": '24 hours'},
            {"value": '48', "title": '48 hours'},
            {"value": '72', "title": '72 hours'},
            {"value": 120, "title": "5 days"},
            {"value": 168, "title": "7 days"},
            {"value": 336, "title": "14 days"},
            {"value": 504, "title": "21 days"},
            {"value": 672, "title": "28 days"},
        ],
        selectedOption: {value: '24', title: '24 hours'} //This sets the default value of the select in the ui
    };

    $scope.changedEnterDate = function() {
        if($scope.alloc_date_type == 'enter_date') {
            $scope.show_expire_date_field = true;
        }
        else {
            $scope.show_expire_date_field = false;
        }
    };

    // Save Course.
    $scope.save = function(addanother) {
        clearUserMesage();
        var addAgain = (angular.isDefined(addanother)) ? true : false;

        if($scope.alloc_course.course_id == null) {
            alert("Please choose course");
            return;
        }

        if($scope.alloc_course.course_supervisor == null || $scope.alloc_course.course_supervisor == '') {
            alert("Please choose course supervisor");
            return;
        }

        if($scope.alloc_course.employee_id == null || $scope.alloc_course.employee_id == '' || $scope.alloc_course.employee_id == null) {
            alert("Please choose employee");
            return;
        }
        
        let employee = $scope.employee_list.filter(function(user){return user.id==$scope.alloc_course.employee_id})[0];
        if(employee.username==null || employee.password==null || employee.UTYPE==0){
            alert("This employee does not appear to have a user account. Please set up a user account for this employee in the users page, and then you will be able to allocate a course.");
            return;
        }

        if($scope.alloc_date_type == 'now') {
            var date = new Date();
            var year = date.getFullYear();
            
            var m = date.getMonth() + 1;
            var month = (m > 9) ? m : '0' + m;
            var day = (date.getDate() > 9) ? date.getDate() : '0' + date.getDate();
            var hours = (date.getHours() > 9) ? date.getHours() : '0' + date.getHours();
            var minutes = (date.getMinutes() > 9) ? date.getMinutes() : '0' + date.getMinutes();
            var seconds = (date.getSeconds() > 9) ? date.getSeconds() : '0' + date.getSeconds();

            var alloc_date = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
            $scope.alloc_course.alloc_date = alloc_date;
        }
        else {
            var date = new Date($scope.enter_alloc_date);
            var year = date.getFullYear();
            
            var m = date.getMonth() + 1;
            var month = (m > 9) ? m : '0' + m;
            var day = (date.getDate() > 9) ? date.getDate() : '0' + date.getDate();

            var alloc_date = year + "-" + month + "-" + day + " 00:00:00" ;
            $scope.alloc_course.alloc_date = alloc_date;
        }
       
        // updated by Alex-cobra -20-04-11
        hrmAPIservice.allocCourse($scope.alloc_course, userData).then(function(response) {
            if(response.data.alloc_course_id == '-99') {// repeat course
                $scope.alloc_course = angular.copy($scope.alloc_course_master);
                $scope.course_supervisor = '';
                $scope.cs.course_supervisor = ''; 
                $scope.employee_name = '';
                $scope.cs.employee_name = '';
                   
                $scope.showMessage = 1;
                $scope.success = 0;
                $scope.userMessage = 'Course allocation failed - You can\'t allocate a course multiple times to an employee.';
            }else if(response.data.alloc_course_id == '-999') {//incomplete user detail
                $scope.alloc_course = angular.copy($scope.alloc_course_master);
                $scope.course_supervisor = '';
                $scope.cs.course_supervisor = ''; 
                $scope.employee_name = '';
                $scope.cs.employee_name = '';
                   
                $scope.showMessage = 1;
                $scope.success = 0;
                $scope.userMessage = 'This employee does not appear to have a user account. Please set up a user account for this employee in the users page, and then you will be able to allocate a course.';
            }
            else {
                if (addAgain) {
                    $scope.alloc_course = angular.copy($scope.alloc_course_master);
                    $scope.course_supervisor = '';
                    $scope.cs.course_supervisor = ''; 
                    $scope.employee_name = '';
                    $scope.cs.employee_name = '';
                       
                    $scope.showMessage = 1;
                    $scope.success = 1;
                    $scope.userMessage = 'Course successfully allocated - You can now allocate another course.';               
                    
                } else {
                    goBack();
                }
            }
        });
        // // // // //
    };

    // Cancel Course.
    $scope.cancel = function() {
        clearUserMesage();
        goBack();
    };

    function goBack() {
        $location.path("/train_dashboard");
    }
}]);