feenance.factory('TransfersApi', function($resource) {
    return $resource(   $API_ROOT + "transfers/:id", {  id:"@id" }, { }    );
});
