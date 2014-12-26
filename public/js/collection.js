/**
 * Created by mark on 13/12/2014.
 */

feenance.factory('Collection', function() {

    return function (api, $initialText) {
        var thisCollection = this;
        this.collection = { data: [] };
        this.promises = {};
        this.api = api;

        /*
         * promises are a set of objects that will contain the index of an account, once the accounts are returned
         * from the ajax call.
         * This private method is called post ajax return to populate all of the promises
         */
        function _updatePromises() {
            console.log("updating promises");

            angular.forEach(thisCollection.collection.data,
                function(value, key)
                {
                    if (thisCollection.promises[value.id] != undefined) {
                        console.log("updating promise    : id: " + value.id + " to index: " + key);
                        thisCollection.promises[value.id].index = key;
                    }
                }
            );
        }

        /*
         * This private method sets a promise and populates it if data is available,
         * otherwise it waits until the promises are fetched and _updatePromises is called
         */
        function _setPromise(promise, id) {
            angular.forEach(thisCollection.collection.data,
                function(value, key)
                {
                    if (value.id == id) {
                        console.log("setting  promise found for index: " + key + " to id: " + id);
                        promise.index = key;
                    }
                }
            );
            return promise;
        }



        if ($initialText) {
            thisCollection.collection.data[0] = {id: null, name: $initialText};
        }

        this.newItem = function()
        {
            return new this.api;
        };

        this.saveItem = function ($item, successCallback, failCallback)
        {
            this.api.update(
                { id:$item.id   },
                $item,
                successCallback,
                failCallback
            );
        }

        this.getData = function()
        {
            return thisCollection.collection.data;
        };

        this.setData = function(data, $initialText)
        {
            angular.extend(thisCollection.collection.data, data);
            if ($initialText) {
                thisCollection.collection.data.splice(0, 0, {id: null, name: $initialText});
            }
            _updatePromises();
        };

        this.add = function(record)
        {
            thisCollection.collection.data.push(record);
            return record;
        };

        this.getPromisedIndex = function (id)
        {
            if (thisCollection.promises[id] != undefined) {
                console.log("found promise       : id: " + id + " to index: " + thisCollection.promises[id].index);
                return thisCollection.promises[id];
            }
                console.log("creating promise    : id: " + id + " to index: -1");
            thisCollection.promises[id] = {index: -1};
            return _setPromise(thisCollection.promises[id], id);
        };

        this.getItemAtIndex = function (index)
        {
            return thisCollection.collection.data[index];
        }

        var results = this.api.get({}, function()
        {
            thisCollection.setData(results.data, $initialText);
        });



    };
});