var navbarServices = angular.module('navbarServices', ['ngResource']);

navbarServices.factory('NavbarApi', ['$resource',
    function($resource) {
        return $resource('api/v1/navs/:navId', {}, {
            list: {
                method: 'GET', params: {}, isArray: false
            }
        });
    }
]);