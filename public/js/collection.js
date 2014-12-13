/**
 * Created by mark on 13/12/2014.
 */

feenance.factory('Collection', function(Notifier, AccountsApi, $filter) {
    var collection = { data: [] };
    var promises = {};
    /*
     * promises are a set of objects that will contain the index of an account, once the accounts are returned
     * from the ajax call.
     * This private method is called post ajax return to populate all of the promises
     */
    function _updatePromises() {
        angular.forEach(collection.data,
            function(value, key)
            {
                if (promises[value.id] != undefined) {
                    promises[value.id].index = key;
                }
            }
        );
    }

    /*
     * This private method sets a promise and populates it if data is available,
     * otherwise it waits until the promises are fetched and _updatePromises is called
     */
    function _setPromise(promise, id) {
        angular.forEach(collection.data,
            function(value, key)
            {
                if (value.id == id) {
                    promise.index = key;
                }
            }
        );
        return promise;
    }

    return function ($initialText) {
        if ($initialText) {
            collection.data[0] = {id: null, name: $initialText};
        }

        this.getData = function()
        {
            return collection.data;
        };
        this.setData = function(data, $initialText)
        {
            angular.extend(collection.data, data);
            if ($initialText) {
                collection.data.splice(0, 0, {id: null, name: $initialText});
            }
            _updatePromises();
        };
        this.add = function(record)
        {
            collection.data.push(record);
            return record;
        };
        this.getPromisedIndex = function (id)
        {
            if (promises[id] != undefined) {
                return promises[id];
            }
            promises[id] = {index:0};
            return _setPromise(promises[id], id);
        },
            this.getItemAtIndex = function (index)
            {
                return collection.data[index];
            }
    };
});

