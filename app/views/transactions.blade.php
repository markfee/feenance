@extends('layouts.default')
@section('content')
<div>

  <div>

    <div class = "row">

      <div class = "col-lg-8"  ng-include="'view/transaction_table.html'"> </div>

      <div class = "col-lg-4">
        <bank-string-map-form accountId=""></bank-string-map-form>
        <transaction-form accountId=""></transaction-form>
      </div>

    </div>

  </div>

</div>

@stop