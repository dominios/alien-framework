var navbarServices = angular.module('navbarServices', ['ngResource']);

navbarServices.factory('NavbarApi', ['$resource',
    function($resource) {
        return $resource('api/v1/navs/:id/:method', {
            id: '@id',
            method: '@method'
        }, {
            list: {
                method: 'GET', params: {}, isArray: false
            },
            update: {
                method: 'PATCH', params: {}
            },
            create: {
                method: 'PUT'
            },
            delete: {
                method: 'DELETE'
            }
        });
    }

]);