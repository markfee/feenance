<nav class="navbar navbar-inverse">
  <div class="navbar-header">
    <a class="navbar-brand" href="{{ URL::to('/') }}">Feenance</a>
    <a class="navbar-brand" href="{{ URL::to('/standing_orders') }}">Standing Orders</a>
    <a class="navbar-brand" href="{{ URL::to('/potential_transfers') }}">Potential Transfers</a>
    <a class="navbar-brand" href="{{ URL::to('/import') }}">Import</a>
    <a class="navbar-brand" href="{{ URL::to('/reports/categories') }}">Category</a>
    <button ng-click="toggleDebug()">Debug</button>
    <button ng-click="clearCache()">Clear Cache</button>
  </div>
</nav>
