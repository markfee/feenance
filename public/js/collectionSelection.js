feenance.factory('CollectionSelection', function() {
  var controller  = null;

  return function($collection, $api, $controller, boundCollection, boundId) {
    this.controller = $controller;
    this.boundId = boundId;
    $controller.collection = $collection;
    $controller.api = $api;
    $controller.collectionSelection = this;
    $controller[boundCollection]   = $collection.collection();
    $controller.selected = { index: -2 };
    $controller[boundId] = -1;

    function isSelected(id) {
      return $controller.selected != undefined && $controller.selected.id == id;
    }

    function isAnewRecord() {
      try {
        if ($controller.selected.id != undefined && $controller.selected.id > 0) {
          return false;
        }
      } catch(e) {
      }
      return true;
    }

    $controller.$watch('selected.index',
      function (new_val, old_val) {
        var directiveName = $controller.directive ? $controller.directive : "";
        console.log("selected.index changed from " + old_val + " to " + new_val + " in " + directiveName);
        if ( new_val != undefined && new_val >=0 ) {
          $controller.selected = $controller.collection.getItemAtIndex(new_val);
        } else {
          var stop = 1;
        }
      }
    );

    $controller.$watch('selected.id',
      function (new_val, old_val) {
        if (new_val != undefined && new_val != old_val) {
          $controller[boundId] = new_val;
        }
      }
    );

    $controller.$watch(boundId,
      function (new_val, old_val) {
        if (new_val != undefined && new_val != old_val) {
          var directiveName = $controller.directive ? $controller.directive : "";
          console.log("BOUND - " + boundId + " changed from " + old_val + " to " + new_val + " in " + directiveName)
          if (!isSelected(new_val))
            $controller.collectionSelection.selectItem(new_val);
        }
      }
    );

    this.getSelected = function()
    {
      return $controller.selected;
    };

    this.rollback = function()
    {
      angular.extend($controller.selected, rollback);
    };

    this.beginEditing = function() {
      rollback = angular.copy($controller.selected);
    };

    this.beginEditingNewItem = function($newItem)
    {
      rollback = $controller.selected;
      $controller.selected = $newItem;
    };

    this.saveItem = function() {
      if (isAnewRecord()) {
        $controller.selected.$save(function (response)
        {
          $controller.selected = $controller.collection.add(response);
          beginEditing();
        });
      } else {
        this.api.update({id:$controller.selected.id}, $controller.selected,
          function(response)
          {
            alert("Updated Successfully (CollectionSelection)");
          }
        );
      }
    };

    this.selectItem = function (id) {
      if (id >= 0) {
        console.log("gettingPromisedIndex for id: " + id + " in " + $controller.directive);
        $controller.selected = $controller.collection.getPromisedIndex(id);
        console.log("gettingPromisedIndex returned: " + $controller.selected.index + " in " + $controller.directive);
      }

      return $controller.selected;
    };
  }
});
