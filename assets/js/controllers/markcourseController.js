// This is created by updated by Alex-cobra on -20-04-06

app.controller("markcourseController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", "$routeParams", "$location", function($scope, $rootScope, cookie, hrmAPIservice, $routeParams, $location) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    $scope.pageTitle = "Review Course";
    $scope.course = {};  
    $scope.answer = {};  
    $scope.course.step = 1;
    $scope.question_id = 0;
    $scope.questionIndex = 0;
    $scope.is_correct = 2;

    $scope.alloc_course_id = $routeParams.id;
    var courseId = '';
    var employeeId = '';

    // Get Alloc Course Data by allocated course ID.
    hrmAPIservice.getAllocCourseById($scope.alloc_course_id).then(function(response) {
        courseId = response.data.alloc_course.course_id;
        employeeId = response.data.alloc_course.employee_user.id;

        hrmAPIservice.getCourseById(courseId, employeeId).then(function(response) {
            $scope.course.coursetitle = response.data.course.course_name;
            $scope.course.coursedescription = response.data.course.course_description;
    
            $scope.course.learnersname = response.data.employee.user.firstname + ' ' + response.data.employee.user.lastname;
            $scope.course.hoursleft = response.data.course.TimeLeft;
            $scope.course.tradingname = response.data.course.department;   // CHANGE
            $scope.course.questions = response.data.course.questions;
    
            $scope.course.userEmailAddress = response.data.employee.user.email;
            $scope.findCurrentQuestion();
            
            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
        });
    });
    
    // updated by Alex-cobra-setting -20-04-07
    // redirect url
    $scope.CloseReview = function() {
        location.href = "#/train_dashboard";
    }
    // // // // //
    
    $scope.findCurrentQuestion = function() {
        $scope.questionIndex = 0;
        var n = 0;
        
        while (n < $scope.course.questions.length) {
            if ($scope.course.questions[n].isAnswered > 0) {
                $scope.questionIndex++;
            }
            n++;
        }
    }

    $scope.back = function() {
        $scope.course.step--;
    }    
    
    $scope.next = function() {
        $scope.course.step++;
    }

    $scope.submitReview = function(questionId, is_correct) {
        var reviewId = is_correct;
        // updated by Alex-cobra-setting -20-04-06
        hrmAPIservice.submitReview(courseId, employeeId, questionId, reviewId).then(function(response) {
            if(response.data.success != 'ok') {
                alert('Review of this question failed.');
                return;
            }

            $scope.questionIndex++;
            if ($scope.questionIndex >= $scope.course.questions.length) {
                $scope.result = response.data;
                $scope.course.step = 'complete';
            } else {
                $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id; 
            }
            $('.question_' + questionId + ' video').remove();
        });
    }
    
}]);