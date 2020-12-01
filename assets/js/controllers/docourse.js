//start updated by Alex-cobra-2020-04-03(added $setInterval)
app.controller("docourseController", ["$scope", "$interval", "$rootScope", "$location", "cookie", "hrmAPIservice", "$routeParams", function($scope,$interval, $rootScope, $location, cookie, hrmAPIservice, $routeParams) {
//end updated by Alex-cobra-2020-04-03
    $scope.prefix_url = document.location.protocol + "//" + document.location.hostname;
    var userData = cookie.checkLoggedIn();
    console.log(userData);
    
    cookie.getPermissions();
    var perm = cookie.getCookie("permissions");
    $scope.isAllowed = true;
    if (!perm)      $scope.isAllowed = false;
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    $scope.pageTitle = "Course";
    $scope.ip_address = "";
    $scope.course = {};
    $scope.answer = {};
    $scope.course.step = 1;
    $scope.question_id = 0;
    $scope.questionIndex = 0;
    $scope.percentageScore = 0;
    $scope.showModalVariable = false;
    $scope.correct_answers = [];
    $scope.progress = {answer:0};
    // updated by Alex-cobra -20-04-03
    $scope.answer1 = '';

    var courseId = $routeParams.courseid;
    var employeeId = $routeParams.employeeid;

    $scope.CloseCourse = function() {
        location.href = "#/trainingcourselist";
    }

    $scope.findCurrentQuestion = function() {
        $scope.questionIndex = 0;
        return;
        var n = 0;

        while (n < $scope.course.questions.length) {
            if ($scope.course.questions[n].isAnswered > 0) {
                $scope.questionIndex++;
            }
            n++;
        }
    }
    
    $.getJSON('https://ipinfo.io', function(data){
        $scope.ip_address = data.ip;
    });

    //start updated by Alex-cobra-2020-04-03(custom aip variables)
    hrmAPIservice.getCourseById(courseId, employeeId).then(function(response) {
        console.log(response);
        if(response.data.error){
            location.href = "#/trainingcourselist";
            return;
        }
        if(response.data.course.course_type === "Questions And Answers") {
            $scope.course.addAnswerDivStyle = {display: 'block'};
            $scope.course.addAnswerDivStyleR = {display: 'none'};
        }
        else {
            $scope.course.addAnswerDivStyle = {display: 'none'};
            $scope.course.addAnswerDivStyleR = {display: 'block'};
        }
        // // // // //

        $scope.course.coursetitle = response.data.course.course_name;
        $scope.course.coursedescription = response.data.course.course_description;

        $scope.course.learnersname = response.data.employee.user.firstname + ' ' + response.data.employee.user.lastname;
        $scope.course.hoursleft = response.data.course.TimeLeft;
        $scope.course.tradingname = response.data.course.department;   // CHANGE
        $scope.course.questions = response.data.course.questions;
        $scope.course.correct_only = response.data.course.correct_only;
        $scope.course.time_limit = response.data.course.time_limit;
        $scope.course.is_randomized = response.data.course.is_randomized;
        $scope.course.display_error_message = response.data.course.display_error_message;
        $scope.course.is_comeback = response.data.course.is_comeback;
        $scope.course.try_again = response.data.course.try_again;
        $scope.course.is_global = response.data.course.is_global;
        $scope.course.go_back = response.data.course.go_back;
        $scope.course.NumQuestions = response.data.course.NumQuestions;

        // updated by Alex-cobra -20-04-04
        $scope.course.userEmailAddress = response.data.employee.user.email;
        // // // // //

        //start updated by Maxim-2020-04-17(custom days and hours variables)
        $scope.course.is_locked = response.data.course.is_locked;
        $scope.course.daysleft = Math.floor( $scope.course.hoursleft.split(":")[0] / 24);
        //$scope.course.timeLeft = Math.floor( $scope.course.hoursleft.split(":")[0] % 24) + ":" + $scope.course.hoursleft.split(":")[1] + ":" + $scope.course.hoursleft.split(":")[2];
        $scope.course.timeLeft = Math.floor( $scope.course.hoursleft.split(":")[0] % 24) + ":" + $scope.course.hoursleft.split(":")[1];
        // end updated by Maxim-2020-04-17
        $scope.findCurrentQuestion();

        for (let index = 0; index < $scope.course.questions.length; index++) {
            const q_element = $scope.course.questions[index];
            for (let index1 = 0; index1 < q_element.answers.length; index1++) {
                const a_element = q_element.answers[index1];
                if(q_element.correct_answer_id == a_element.answer_id) {
                    $scope.correct_answers.push(a_element.title);
                }
            }
        }
        if($scope.course.is_comeback==1){
            hrmAPIservice.getEmployeeCourseLastAttempt(courseId, employeeId).then(function(response) {
                console.log('last attept questiosn and submitted answers');
                console.log(response.data.course.questions);
                let q = response.data.course.questions;
                let isFinished = 1;
                for(let i=0; i<q.length; i++){
                    if(q[i].answers[0]==false){
                        isFinished = 0;
                        $scope.questionIndex = i;
                        $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                        $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                        
                        $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex+1) + " of " + $scope.course.NumQuestions + "</span>";
                        break;
                    }
                }
                if(isFinished){
                    
                    $scope.questionIndex = 0;
                    $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                    $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                    $scope.randomized = "<span class = 'green'> Question " + 1 + " of " + $scope.course.NumQuestions + "</span>";
                }
            });
        }else {
            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
            console.log($scope.question_id);
            
            $scope.randomized = "<span class = 'green'> Question " + 1 + " of " + $scope.course.NumQuestions + "</span>";
        }
        console.log($scope.correct_answers);
    }, function (data) {
        console.log(data);

    });
    //end updated by Alex-cobra-2020-04-03
    var promise = null;
    $scope.courseproceed = function() {
        $scope.course.step = 2;
        var today = new Date();
        //var started_date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()+' '+today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        hrmAPIservice.startCourse(courseId, employeeId).then(function(response) {
            if (promise != null)
                $interval.cancel(promise);
            if($scope.course.time_limit == 0 || $scope.course.correct_only == 1)
                return ;
            else{
                $scope.countDown = $scope.course.time_limit;
    
                promise = $interval(doCountDown, 1000);
            }
        });
    }
    $scope.$on('$destroy', function(e) {
        $interval.cancel(promise);
    });
    
    let doCountDown = function(){
        console.log($scope.countDown);
        
        if($scope.course.step === 'complete' || $scope.course.questions[$scope.questionIndex].media_type == 1 )
        {
            $scope.timerCount = "<span></span>";
            $interval.cancel(promise);
            return;
        }
        
        let min = Math.floor($scope.countDown--/ 60);
        if ($scope.countDown % 60 === 59){
            if (min < 10){
                min = "0" + min;
            }else{
                min = Math.floor($scope.countDown/ 60) - 1
            }
        }else{
            if (min < 10){
                min = "0" + min ;
            }
        }

        if ($scope.countDown % 60 < 10){
            var sec = "0" + $scope.countDown % 60;
        }else{
            var sec = $scope.countDown % 60;
        }
        
        if($scope.countDown === 3)
        {
            if($scope.course.display_error_message==1){
                if($scope.course.questions[$scope.questionIndex].correct_answer_id==$scope.answer[$scope.course.questions[$scope.questionIndex].question_id]){
                    $scope.is_correct_answer = true;
                }
                else if($scope.answer[$scope.course.questions[$scope.questionIndex].question_id]>0) {
                    $scope.is_correct_answer = false;
                    $scope.showModalVariable = true;
                }
            }
        }
        if ($scope.countDown === 0){
            if($scope.course.display_error_message==1) $scope.showModalVariable = false;
            $scope.timerCount = "<span></span>";
            let questionId = $scope.course.questions[$scope.questionIndex].question_id;
            let answerId = $scope.answer[questionId];
            $interval.cancel(promise);

            hrmAPIservice.submitAnswer(courseId, employeeId, questionId, 0, $scope.ip_address, null).then(function(response) {
                if ($scope.questionIndex >= $scope.course.questions.length - 1) {
                    $scope.result = response.data;
                    $scope.course.step = 'complete';
                    $interval.cancel(promise);
                } else {
                    $scope.countDown = $scope.course.time_limit;
                    promise = $interval(doCountDown, 1000);
                    $scope.questionIndex++;
                    $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                    $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                }
                $('.question_' + $scope.questionIndex + ' video').remove();
                $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex + 1) + " of " + $scope.course.NumQuestions + "</span>"

            });
        }else{
            if($scope.countDown > ($scope.course.time_limit * 67/100))
                $scope.timerCount = "<span class='green'>Time Limit : " + min + ":" + sec + "</span>";
            else {
                if($scope.countDown > ($scope.course.time_limit * 34/100))
                    $scope.timerCount = "<span class='orange'>Time Limit : " + min + ":" + sec + "</span>";
                else
                    $scope.timerCount = "<span class='red'>Time Limit : " + min + ":" + sec + "</span>";
            }

        }
    }

    $scope.back = function() {
        if (promise != null)
            $interval.cancel(promise);
        if($scope.questionIndex>0 && $scope.course.go_back==1){
            $scope.questionIndex--;
            $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
            if($scope.course.time_limit != 0){
                $scope.countDown = $scope.course.time_limit;
                promise = $interval(doCountDown, 1000);
            }
        } else {
            $scope.course.step--;
        }
    }

    $scope.next = function() {
        $scope.course.step++;
    }
    
    $scope.course_logout = function(){
        console.log('logout');
        if (promise != null)
            $interval.cancel(promise);
        location.href = "#/logout";
    }

    //start updated by Alex-cobra-2020-04-13(custom question style)

    $scope.submitAnswer = function(question) {
        let questionId = question.question_id;
        // if($scope.course.time_limit != 0 && $scope.course.correct_only != 1){
        //     $scope.countDown = $scope.course.time_limit;

        //     promise = $interval(function(){
        //         console.log('.'+$scope.countDown);
        //         var min = Math.floor($scope.countDown--/ 60);
        //         if ($scope.countDown % 60 === 59){
        //             if (min < 10){
        //                 min = "0" + (min - 1);
        //             }else{
        //                 min = Math.floor($scope.countDown/ 60) - 1
        //             }
        //         }else{
        //             if (min < 10){
        //                 min = "0" + min ;
        //             }
        //         }

        //         if ($scope.countDown % 60 < 10){
        //             var sec = "0" + $scope.countDown % 60;
        //         }else{
        //             var sec = $scope.countDown % 60;
        //         }
        //         if($scope.course.step === 'complete' || $scope.course.questions[$scope.questionIndex].media_type == 1 )
        //         {
        //             $scope.timerCount = "<span></span>";
        //             return;
        //         }
        //         if ($scope.countDown === 0){
        //             $scope.timerCount = "<span></span>";

        //             hrmAPIservice.submitAnswer(courseId, employeeId, questionId, null).then(function(response) {
        //                 if ($scope.questionIndex >= $scope.course.questions.length-1) {
        //                     $scope.result = response.data;
        //                     $scope.course.step = 'complete';
        //                 } else {
        //                     $scope.countDown = $scope.course.time_limit;
        //                     $scope.questionIndex++;
        //                     $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
        //                     $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
        //                 }
        //                 $('.question_' + questionId + ' video').remove();
        //                 $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex + 1) + " of " + $scope.course.NumQuestions + "</span>"
        //             });
        //         }else{
        //             if($scope.countDown > ($scope.course.time_limit * 67/100))
        //                 $scope.timerCount = "<span class='green'>Time Limit : " + min + ":" + sec + "</span>";
        //             else {
        //                 if($scope.countDown > ($scope.course.time_limit * 34/100))
        //                     $scope.timerCount = "<span class='orange'>Time Limit : " + min + ":" + sec + "</span>";
        //                 else
        //                     $scope.timerCount = "<span class='red'>Time Limit : " + min + ":" + sec + "</span>";
        //             }
        //         }
        //     }, 1000);
        // }

        var answerId = $scope.answer[questionId];
        if (answerId === 0) {
            return;
        }
        if($scope.course.display_error_message==1){
            if($scope.course.questions[$scope.questionIndex].correct_answer_id==$scope.answer[$scope.course.questions[$scope.questionIndex].question_id]){
                $scope.is_correct_answer = true;
                console.log("promise: "+promise);
                if (promise != null)
                    $interval.cancel(promise);
                
                hrmAPIservice.submitAnswer(courseId, employeeId, questionId, answerId, $scope.ip_address).then(function(response) {
                    if ($scope.course.try_again === 1){
                        
                        $scope.countDown = $scope.course.time_limit;
                        if (answerId != $scope.course.questions[$scope.questionIndex].correct_answer_id){
                            $scope.try_again = "<span class='alert alert-danger'>Please select correct answer.</span>"
                        }else{
                            $scope.try_again = "<span></span>"
                            $scope.questionIndex++;
                            $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                            if ($scope.questionIndex >= $scope.course.questions.length) {
                                $scope.result = response.data;
                                $scope.course.step = 'complete';
                            } else {
                                $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                                promise = $interval(doCountDown, 1000);
                            }
                            $('.question_' + questionId + ' video').remove();
                        }
                    }else{
                        $scope.try_again = "<span></span>";
                        
                        $scope.countDown = $scope.course.time_limit;
                        $scope.percentageScore = response.data.percentageScore;
                        $scope.questionIndex++;
                        $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                        if ($scope.questionIndex >= $scope.course.questions.length) {
                            $scope.result = response.data;
                            $scope.course.step = 'complete';
                        } else {
                            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                            if($scope.course.time_limit!=0){
                                promise = $interval(doCountDown, 1000);
                            }
                        }
                        $('.question_' + questionId + ' video').remove();
                        $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex + 1) + " of " + $scope.course.NumQuestions + "</span>"
                    }
                });
            }
            else{
                $scope.is_correct_answer = false;
                $scope.showModalVariable = true;
            }
        }else{
            console.log("promise: "+promise);
            if (promise != null)
                $interval.cancel(promise);
            hrmAPIservice.submitAnswer(courseId, employeeId, questionId, answerId, $scope.ip_address).then(function(response) {
                    if ($scope.course.try_again === 1){
                        
                        $scope.countDown = $scope.course.time_limit;
                        if (answerId != $scope.course.questions[$scope.questionIndex].correct_answer_id){
                            $scope.try_again = "<span class='alert alert-danger'>Please select correct answer.</span>"
                        }else{
                            $scope.try_again = "<span></span>"
                            $scope.questionIndex++;
                            $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                            if ($scope.questionIndex >= $scope.course.questions.length) {
                                $scope.result = response.data;
                                $scope.course.step = 'complete';
                            } else {
                                $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                                promise = $interval(doCountDown, 1000);
                            }
                            $('.question_' + questionId + ' video').remove();
                        }
                    }else{
                        $scope.try_again = "<span></span>";
                        
                        $scope.countDown = $scope.course.time_limit;
                        $scope.percentageScore = response.data.percentageScore;
                        $scope.questionIndex++;
                        $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                        if ($scope.questionIndex >= $scope.course.questions.length) {
                            $scope.result = response.data;
                            $scope.course.step = 'complete';
                        } else {
                            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                            if($scope.course.time_limit!=0){
                                promise = $interval(doCountDown, 1000);
                            }
                        }
                        $('.question_' + questionId + ' video').remove();
                        $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex + 1) + " of " + $scope.course.NumQuestions + "</span>"
                    }
                });
        }
    }
    //end updated by Alex-cobra-2020-04-13


    //start updated by Alex-cobra-2020-04-13(custom question style)
    $scope.submitAnswer1 = function(question, answer1) {
        var answer = answer1;
        let questionId = question.question_id;
        if (answer == '') {
            alert('Please input your answer.');
            return;
        }

        if (promise != null)
            $interval.cancel(promise);
        if($scope.course.time_limit > 0 ){

            $scope.countDown = $scope.course.time_limit;
            promise = $interval(function(){

                var min = Math.floor($scope.countDown--/ 60);
                if ($scope.countDown % 60 === 59){
                    if (min < 10){
                        min = "0" + min;
                    }else{
                        min = Math.floor($scope.countDown/ 60) - 1
                    }
                }else{
                    if (min < 10){
                        min = "0" + min ;
                    }
                }

                if ($scope.countDown % 60 < 10){
                    var sec = "0" + $scope.countDown % 60;
                }else{
                    var sec = $scope.countDown % 60;
                }
                if($scope.course.step === 'complete')
                {
                    $scope.timerCount = "<span></span>";
                    return;
                }
                if (min == 0 && sec == 0){
                    $scope.timerCount = "<span></span>";
                    $scope.questionIndex++;
                    $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                    hrmAPIservice.submitAnswer1(courseId, employeeId, questionId, '-999').then(function(response) {
                        $scope.countDown = $scope.course.time_limit;
                        if ($scope.questionIndex >= $scope.course.questions.length) {
                            $scope.result = response.data;
                            $scope.course.step = 'complete';
                        } else {
                            $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                        }
                        $('.question_' + questionId + ' video').remove();

                    });
                }else{
                    if($scope.countDown > ($scope.course.time_limit * 67/100))
                        $scope.timerCount = "<span class='green'>Time Limit : " + min + ":" + sec + "</span>";
                    else {
                        if($scope.countDown > ($scope.course.time_limit * 34/100))
                            $scope.timerCount = "<span class='orange'>Time Limit : " + min + ":" + sec + "</span>";
                        else
                            $scope.timerCount = "<span class='red'>Time Limit : " + min + ":" + sec + "</span>";
                    }
                }
            }, 1000);
        }


        hrmAPIservice.submitAnswer1(courseId, employeeId, questionId, answer, $scope.ip_address).then(function(response) {
            if ($scope.course.try_again == 1){

                if (answerId != $scope.course.questions[$scope.questionIndex].correct_answer_id){
                    $scope.try_again = "<span class='alert alert-danger'>Please select correct answer.</span>"
                }else{
                    $scope.try_again = "<span></span>"
                    $scope.questionIndex++;
                    $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                    if ($scope.questionIndex >= $scope.course.questions.length) {
                        $scope.result = response.data;
                        $scope.course.step = 'complete';
                    } else {
                        $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                    }
                    $('.question_' + questionId + ' video').remove();
                }
            }else{
                $scope.try_again = "<span></span>"

                $scope.questionIndex++;
                $scope.progress.answer = parseInt($scope.questionIndex * 100 / $scope.course.questions.length);
                if ($scope.questionIndex >= $scope.course.questions.length) {
                    $scope.result = response.data;
                    $scope.course.step = 'complete';
                } else {
                    $scope.question_id = $scope.course.questions[$scope.questionIndex].question_id;
                }
                $('.question_' + questionId + ' video').remove();
                $scope.randomized = "<span class = 'green'> Question " + ($scope.questionIndex + 1) + " of " + $scope.course.NumQuestions + "</span>"
            }
        });

    }
    //end updated by Alex-cobra-2020-04-13




    // Alex-cobra -20-05-03
    $scope.getPdfSrc = function (pdffile) {
        if(pdffile!=null)
            return 'assets/templates/pdfviewer.html?file=' + pdffile;
    }

    $scope.modal_close = function () {
        // $('.wrong-modal').css('display', 'none');
        // $('.modal-backdrop.in').css('display', 'none');
        $scope.showModalVariable = false;
    }

    $scope.showModal = function (q) {
        // if($scope.course.correct_only == 1 ){

        //     if($scope.answer[q.question_id] == q.correct_answer_id) {
        //         $scope.is_correct_answer = true;
        //     } else {
        //         $scope.showModalVariable = true;
        //         $scope.is_correct_answer = false;
        //     }
        // }else if($scope.course.display_error_message == 1 && $scope.course.time_limit==0){
        
        //     if($scope.answer[q.question_id] == q.correct_answer_id) {
        //         $scope.is_correct_answer = true;
        //         $scope.showModalVariable = true;
        //     } else {
        //         $scope.showModalVariable = true;
        //         $scope.is_correct_answer = false;
        //     }
        // }
        // else {
        //     $scope.showModalVariable = false;
        // }
    }

    $scope.getCorrectAnswerContent = function() {
        if($scope.is_correct_answer)
            return 'Ok, Your answer is correct.';
        else
            return 'Your answer is incorrect. Please try again.';
    }

}]);
