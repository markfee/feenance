@extends('layouts.default')
@section('content')
<div>

  <div data-ng-controller="FeenanceController">

    <button ng-click="toggleDebug()">Debug</button>

    <div class = "row">

      <div class = "col-lg-10">

        <standing-orders-table> Standing Orders Table Here </standing-orders-table>

      </div>

      <div class = "col-lg-2">

        <standing-order standingOrderId=""> Edit Standing Orders Table </standing-order>

      </div>
    </div>

  </div>

</div>

  <p>

  </p>

@stop