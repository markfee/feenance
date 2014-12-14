feenance.factory('CollectionSelection', function() {
  var controller  = null;

  return function($collection, $api, $controller, boundCollection, boundId) {
    this.controller = $controller;
    this.boundId = boundId;
    $controller.collection = $collection;
    this.api = $api;
    $controller.collectionSelection = this;
    $controller[boundCollection]   = $collection.collection();
    $controller.selected = { index: -2 };
    $controller[boundId] = -1;

    $controller.log = function($message) {
      var directiveName = ($controller.directive ? $controller.directive : "_") + "                        ";
      console.log(directiveName.substr(0, 20) + ": " + $message);
    }

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
        $controller.log("selected.index changed from " + old_val + " to " + new_val);
        if ( new_val != undefined && new_val >=0 ) {
          $controller.selected = $controller.collection.getItemAtIndex(new_val);
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
          $controller.log("Watched " + boundId + " changed from " + old_val + " to " + new_val + " in ");
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

        $controller.selected.$save( function (response)
        {
          $controller.selected = $controller.collection.add(response);
          $controller.collectionSelection.beginEditing();
        });

      } else {

        this.api.update(
            {id:$controller.selected.id},
            $controller.selected,
            function(response)
            {
              alert("Updated Successfully (CollectionSelection)");
            },
            function(response)
            {
              alert("Updated Successfully (CollectionSelection)");
            }
        );

      }
    };

    this.selectItem = function (id) {
      if (id >= 0) {
        $controller.log("gettingPromisedIndex for id: " + id);
        $controller.selected = $controller.collection.getPromisedIndex(id);
        $controller.log("gettingPromisedIndex returned: " + $controller.selected.index);
      }

      return $controller.selected;
    };
  }
});
