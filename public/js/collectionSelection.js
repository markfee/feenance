feenance.factory('CollectionSelection', function() {
  var controller  = null;

  return function($collection, $api, $controller, boundCollection, boundId) {
    this.controller = $controller;
    $controller.collection = $collection;
    $controller.api = $api;
    $controller.collectionSelection = this;
    $controller[boundCollection]   = $collection.collection();
    $controller.selected = null;

    $controller.$watch('selected.index',
      function (new_val, old_val) {
        if (new_val != undefined) {
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

    $controller.$watch(boundId,
      function (new_val, old_val) {
        if (new_val != undefined && new_val != old_val) {
          if (!isSelected(new_val))
            selectItem(new_val);
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
      $controller.selected = $controller.collection.getPromisedIndex(id);
      return $controller.selected;
    };
  }
});
