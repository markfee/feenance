<nav class="navbar navbar-inverse">
  <div class="navbar-header">
    <a class="navbar-brand" href="{{ URL::to('/') }}">Feenance</a>
    <a class="navbar-brand" href="{{ URL::to('/standing_orders') }}">Standing Orders</a>
    <a class="navbar-brand" href="{{ URL::to('/import') }}">Import</a>
    <a class="navbar-brand" href="{{ URL::to('/categories') }}">Category</a>
    <button ng-click="toggleDebug()">Debug</button>
  </div>
</nav>
