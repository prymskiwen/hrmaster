/*app.service("cookie", function($rootScope, $location, $cookies, $route) {
    var obj = {};
    return obj.checkLoggedIn = function(returnStatus) {
        returnStatus = (angular.isDefined(returnStatus)) ? returnStatus : false;

        var userData = this.getCookie('user');
        //console.log(userData + " : " + returnStatus);
        //console.log(userData);
        if (userData === null) {
            $rootScope.isLoggedin = 0;
            if (returnStatus) {
                return false;
            } else {
                $location.path('/');
                return;
            }
        } else {
            $rootScope.isLoggedin = 1;
            $rootScope.showHeader = 0;
        }
        return userData;
    }, obj.getPermissions = function() {
        var perms = this.getCookie("permissions");
        $rootScope.perms = {};
        var controller = $route.current.controller.replace("Controller", "");
        angular.forEach(perms, function(obj, key) {
            var cntl = obj.c;
            var id = obj.id;
            
            $rootScope.perms[cntl] = {}, 
            $rootScope.perms[cntl].read = angular.isDefined(obj.r) ? obj.r : 0, 
            $rootScope.perms[cntl].write = angular.isDefined(obj.w) ? obj.w : 0, 
            $rootScope.perms[cntl].delete = angular.isDefined(obj.d) ? obj.d : 0, 
            controller != cntl || 0 != $rootScope.perms[cntl].read || $location.path("/")   
        });
        
        
    }, obj.setCookie = function(name, value, lengthHours) {
        lengthHours = .0000025;
        var d = new Date;
        //    sessionHours = 60 * lengthHours * 60 * 1e3;
       
        var sessionHours = lengthHours * 60 * 60 * 1000;    
        d.setTime(d.getTime() + sessionHours);
        var expires = d.toUTCString();
        console.log("session hours: "+sessionHours);
        
        
        $cookies.putObject(name, value, {
            expires: expires
        });
        console.log("value: "+value);
        localStorage.setItem(name, JSON.stringify(value));
    }, obj.resetCookie = function() {
        var data, cList = ["user", "permissions"],
            _this = this;
        angular.forEach(cList, function(value, key) {
            !1 !== (data = _this.getCookie(value)) && _this.setCookie(value, data)
        })
    }, obj.getCookie = function(name) {
        //var cook = $cookies.getObject(name);
        var cook = JSON.parse(localStorage.getItem(name));
        return (angular.isDefined(cook)) ? cook : false;
    }, obj.deleteCookie = function(name) {
        // $cookies.remove(name)
        localStorage.removeItem(name);
    }, obj.checkCookie = function(cookieName) {
        var callSearch = obj.getCookie(cookieName);
        return "" != callSearch && callSearch
    }, obj
})*/


app.service('cookie', function($rootScope, $location, $cookies, $route) {

    var obj = {};

    obj.checkLoggedIn = function(returnStatus) {
        returnStatus = (angular.isDefined(returnStatus)) ? returnStatus : false;
        var userData = this.getCookie('user');
        if (userData === false) {
            $rootScope.isLoggedin = 0;
            if (!returnStatus) {
                $location.path('/');
                return;
            }
        } else {
            $rootScope.isLoggedin = 1;
            $rootScope.showHeader = 0;
        }
        return userData;
    }

    // obj.getPermissions = function() {
    //     var perms = this.getCookie('permissions');
    //     $rootScope.perms = {};

    //     var controller = $route.current.controller.replace("Controller", "");
    //     angular.forEach(perms, function(obj, key) {
    //         var cntl = obj.c;
    //         $rootScope.perms[cntl] = {};
    //         $rootScope.perms[cntl]['read']    = (angular.isDefined(obj.r)) ? obj.r : 0;
    //         $rootScope.perms[cntl]['write']   = (angular.isDefined(obj.w)) ? obj.w : 0;
    //         $rootScope.perms[cntl]['delete']  = (angular.isDefined(obj.d)) ? obj.d : 0;

    //         if (controller == cntl) {
    //             if ($rootScope.perms[cntl]['read'] == 0) {
    //                 $location.path('/');
    //                 return;
    //             }
    //         }
    //     });
    // }

    obj.getPermissions = function() {
        var perms = obj.getWithExpiry("permissions");
        if(!perms){
            localStorage.clear();
            $location.path('/');
            return;
        }
        console.log(perms);
        $rootScope.perms = {};

        var controller = $route.current.controller.replace("Controller", "");
        angular.forEach(perms, function(obj, key) {
            var cntl = obj.c;
            $rootScope.perms[cntl] = {};
            $rootScope.perms[cntl]['read']    = (angular.isDefined(obj.r)) ? obj.r : 0;
            $rootScope.perms[cntl]['write']   = (angular.isDefined(obj.w)) ? obj.w : 0;
            $rootScope.perms[cntl]['delete']  = (angular.isDefined(obj.d)) ? obj.d : 0;

            if (controller == cntl) {
                if ($rootScope.perms[cntl]['read'] == 0) {
                    $location.path('/');
                    return;
                }
            }
        });
    }

    obj.setCookie = function(name, value, lengthHours) {
        lengthHours = 0.75;
        var d = new Date();

        var sessionHours = lengthHours * 60 * 60 * 1000;

        d.setTime(d.getTime() + sessionHours);
        var expires = d.toUTCString();
        $cookies.putObject(name, value, {'expires': expires});
        obj.setWithExpiry(name, value, sessionHours);
        //localStorage.setItem(name, JSON.stringify({value: value, expiry: expires}));
    }
    
    obj.setWithExpiry = function(key, value, ttl) {
    	const now = new Date();
    	const item = {
    		value: value,
    		expiry: now.getTime() + ttl,
    	}
    	localStorage.setItem(key, JSON.stringify(item))
    }
    obj.getWithExpiry = function(key) {
    	const itemStr = localStorage.getItem(key)
    	// if the item doesn't exist, return null
    	if (!itemStr)
    		return null;
    	const item = JSON.parse(itemStr)
    	const now = new Date()
    	// compare the expiry time of the item with the current time
    	if (now.getTime() > item.expiry) {
    		// If the item is expired, delete the item from storage
    		// and return null
    		localStorage.removeItem(key)
    		return null
    	}
    	return item.value
    }

    obj.resetCookie = function() {
        var cList = ['user','permissions'];
        var data;
        var _this = this;
        angular.forEach(cList, function(value, key) {
            data = _this.getCookie(value);
            if (data !== false) {
                _this.setCookie(value, data);
            }
        });
    }

    obj.getCookie = function(name) {
        //var cook = $cookies.getObject(name);
        //var cook = JSON.parse(localStorage.getItem(name));
        var cook = obj.getWithExpiry(name);
        return (angular.isDefined(cook)) ? cook : false;
    }

    obj.deleteCookie = function(name) {
        $cookies.remove(name);
        localStorage.clear();
    }

    obj.checkCookie = function(cookieName) {
        var callSearch = obj.getCookie(cookieName);
        return (callSearch == "") ? false : callSearch;
    }

    return obj;
});
