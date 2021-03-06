<!doctype html>
<html ng-app="feenance" data-ng-controller="FeenanceController">
<head>
  @include('includes.head')
</head>
<body>
  <div class="container">
    <header class="row">
      @include('includes.header')
    </header>

    <div id="main" class="row">
        @yield('content')
    </div>

    <footer class="row">
      @include('includes.footer')
    </footer>

  </div>
</body>
@include('includes.scripts')
</html>
