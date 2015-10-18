var navbarServices = angular.module('navbarServices', ['ngResource']);

navbarServices.factory('NavbarApi', ['$resource',
    function($resource) {
        return $resource('api/v1/nav/:method/:navId', {}, {
            getList: {
                method: 'GET', params: {method: 'getList'}, isArray: true
            }
        });
    }
]);