app.controller("EditAllocCourseController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", "$routeParams",  function($scope, $rootScope, cookie, hrmAPIservice, $routeParams) {
    var userData = cookie.checkLoggedIn(), courseCategory = [];
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    
    console.log(perm);
    
    if (perm['57'] == null || perm['57']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['57'].w == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }

    $scope.cs = {};
    $scope.cs.selectedItem  = null;
    $scope.cs.searchText    = null;
    $scope.loading = 1;
    $scope.pageTitle = "";
    $scope.course_keyword = "";
    $scope.alloc_course_id = $routeParams.id;
    $scope.alloc_course = {
        course_id: '',
        course_supervisor: '',
        course_description: '',
        employee_id: '',
        alloc_date: '',
        can_add_extra: 0,
        expire_hours: 0,
        extra_hours: 0,
        is_sending_email: true,
        daily_reminder_email_receiver: null,
        status: 0,
        user_id: userData.id,
    };
    
    const clearUserMesage = function(){
        $scope.showMessage = 0;
        $scope.success = 0;
        $scope.userMessage = '';                  
    } 

    $scope.expire_hours = {
        availableOptions: [
            {"value": '24', "title": '24 hours'},
            {"value": '48', "title": '48 hours'},
            {"value": '72', "title": '72 hours'},
            {"value": '120', "title": "5 days"},
            {"value": '168', "title": "7 days"},
            {"value": '336', "title": "14 days"},
            {"value": '504', "title": "21 days"},
            {"value": '672', "title": "28 days"},
        ],
        selectedOption: {value: '24', title: '24 hours'} //This sets the default value of the select in the ui
    };

    $scope.extra_hours = {
        availableOptions: [
            {"value": '0', "title": 'Select time'},
            {"value": '1', "title": '1 hour'},
            {"value": '24', "title": '1 day'},
            {"value": '168', "title": '7 days'},
            {"value": '336', "title": "14 days"},
            {"value": '672', "title": "28 days"},
        ],
        selectedOption: {value: '0', title: 'Select time'} //This sets the default value of the select in the ui
    };

    // Get Course List.
    /*hrmAPIservice.getCourseData(userData.id).then(function(response) {
        var courses = response.data.courses;
        $scope.course_list = courses;

    });*/



    $scope.show_expire_date_field = true;
    $scope.alloc_date_type = 'enter_date';

    $scope.course_name_list = [];
    $scope.course_list = [];

    $scope.user_name_list = [];
    $scope.user_list = [];
    $scope.allusers = [];

    // Get Course List.
    hrmAPIservice.getCourseData(userData.id, true).then(function(response) {
        //var courses = response.data.courses;
        
        var courses = response.data.courses.filter(function(course) {
            return course.status == 1;
        });        

        $scope.course_list = courses;
        
        hrmAPIservice.getActiveUsers(userData, 0).then(function(response) {
            //$scope.allusers = response.data;
            $scope.allusers = response.data.map(function(usr) {
                return {
                    value: usr.id,
                    display: usr.firstname + " " + usr.lastname
                };
            });
        });
    });
    
    // Get Alloc Course Data by selected ID.
    hrmAPIservice.getAllocCourseById($scope.alloc_course_id).then(function(response) {
        $scope.alloc_course = angular.copy(response.data.alloc_course);
        console.log($scope.alloc_course);
        if(response.data.message!=""){
            console.log(response.data.message);
            $scope.showMessage = 1;
            $scope.userMessage = response.data.message;
        }
        $scope.course_name = $scope.alloc_course.course.course_name;
        $scope.enter_alloc_date = new Date($scope.alloc_course.alloc_date);
        $scope.course_supervior = $scope.alloc_course.supervisor_user.firstname + " " + $scope.alloc_course.supervisor_user.lastname;
        $scope.employee_name = $scope.alloc_course.employee_user.firstname + " " + $scope.alloc_course.employee_user.lastname;
        $scope.alloc_course.course_id = response.data.alloc_course.course_id;
        $scope.alloc_course.course_description =  response.data.alloc_course.course.course_description;
        $scope.alloc_course.course_supervisor = response.data.alloc_course.supervisor_user.id;
        $scope.alloc_course.can_add_extra = 0;
        $scope.alloc_course.daily_reminder_email_receiver = response.data.alloc_course.daily_reminder_email_receiver;
        console.log(response.data.alloc_course.supervisor_user);
        console.log($scope.alloc_course);

        for(i = 0; i < $scope.expire_hours.availableOptions.length; i++) {
            var item = $scope.expire_hours.availableOptions[i];
            if(item["value"] == $scope.alloc_course.expire_hours) {
                $scope.expire_hours.selectedOption = item;
                break;
            }
        }
        if($scope.isAllowed){
            $scope.alloc_course.can_add_extra = 1;
            for(i = 0; i < $scope.extra_hours.availableOptions.length; i++) {
                var item = $scope.extra_hours.availableOptions[i];
                if(item["value"] == $scope.alloc_course.extra_hours) {
                    $scope.extra_hours.selectedOption = item;
                    break;
                }
            }
        }
        $scope.cs.course_supervisor = {
            'value': $scope.alloc_course.supervisor_user.id,
            'display': $scope.alloc_course.supervisor_user.firstname + " " + $scope.alloc_course.supervisor_user.lastname
        };
        $scope.cs.employee_name = {
            'value': $scope.alloc_course.employee_user.id,
            'display': $scope.alloc_course.employee_user.firstname + " " + $scope.alloc_course.employee_user.lastname
        };
    });
    
    $scope.selectEmployee = function() { 
        $scope.alloc_course.employee_id = $scope.cs.employee_name.value;
    }
    
    $scope.selectSupervisor = function() { 
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

    // $scope.doChangeCourseName = function(typedthings) {
    //     if ($scope.loading === 1) {
    //         $scope.loading = 0;
    //         return;
    //     }

    //     if(typedthings != null && typedthings.length > 0) {
    //         hrmAPIservice.searchCourse(typedthings).then(function(response) {
    //             var courses = response.data.courses;
    //             $scope.course_list = courses;

    //             var names = [];
    //             for(var i = 0; i < $scope.course_list.length; i++) {
    //                 var course_name = courses[i].course_name;
    //                 var course_id = courses[i].course_id;
    //                 names[i] = course_name;
    //             }

    //             $scope.course_name_list = names;
    //         });
    //     }
    // }

    // $scope.doSelectedCourseName = function(suggestion){
    //     $scope.alloc_course.course_description = '';
    //     for(let i=0; i<$scope.course_list.length; i++) {
    //         if ($scope.course_list[i].course_name == suggestion) {
    //             $scope.course_name = $scope.course_list[i].course_name;
    //             $scope.alloc_course.course_id = $scope.course_list[i].course_id;
    //             $scope.alloc_course.course_description = $scope.course_list[i].course_description;
    //             break;
    //         }
    //     }
    // }

    // $scope.doChangeCourseSuperior = function(typedthings) {
    //     if(typedthings != null && typedthings.length > 0) {
    //         hrmAPIservice.searchUser(typedthings).then(function(response) {
    //             var users = response.data.users;
    //             $scope.user_list = users;

    //             var names = [];
    //             for(var i = 0; i < $scope.user_list.length; i++) {
    //                 var username = users[i].username;
    //                 var firstname = users[i].firstname;
    //                 var lastname = users[i].lastname;

    //                 names[i] = firstname + " " + lastname + "(" + username + ")";
    //             }

    //             $scope.user_name_list = names;
    //         });
    //     }
    // }

    // $scope.doSelectedCourseSuperior = function(suggestion) {
    //     var array = suggestion.split("(");

    //     var name = array[0];
    //     var username = array[1];

    //     $scope.course_supervior = name;
    //     username = username.replace(")", "");

    //     for(var i = 0; i < $scope.user_list.length; i++) {
    //         var user = $scope.user_list[i];
    //         if(user.username == username) {
    //             $scope.alloc_course.course_supervisor = user.id;
    //             break;
    //         }
    //     }
    // }

    // $scope.doSelectedEmployee = function(suggestion) {
    //     var array = suggestion.split("(");

    //     var name = array[0];
    //     var username = array[1];

    //     $scope.employee_name = name;
    //     username = username.replace(")", "");

    //     for(var i = 0; i < $scope.user_list.length; i++) {
    //         var user = $scope.user_list[i];
    //         if(user.username == username) {
    //             $scope.alloc_course.employee_id = user.id;
    //             break;
    //         }
    //     }
    // }

    $scope.changedEnterDate = function() {
        if($scope.alloc_date_type == 'enter_date') {
            $scope.show_expire_date_field = true;
        }
        else {
            $scope.show_expire_date_field = false;
        }
    };

    // Save Course.
    $scope.save = function() {
        clearUserMesage();

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

        $scope.alloc_course.expire_hours = $scope.expire_hours.selectedOption.value;
        if($scope.isAllowed){
            $scope.alloc_course.can_add_extra = 1;
            $scope.alloc_course.extra_hours = $scope.extra_hours.selectedOption.value;
        }
        console.log($scope.alloc_course);
        hrmAPIservice.updateAllocCourse($scope.alloc_course).then(function(response) {
            goBack();
        });
    };

    // Cancel Course.
    $scope.cancel = function() {
        clearUserMesage();
        goBack();
    };

    function goBack() {
        location.href = "#/train_dashboard";
    }
}]);
