@extends('layouts.default')
@section('content')
<div>

  <div>

    <account-selector ng-model="account" account-id="2"> </account-selector>

    <div class = "row">

      <div class = "col-lg-8"  ng-include="'view/transaction_table.html'"> </div>

      <div class = "col-lg-4">
        <new-map accountId=""></new-map>
        <transaction-form accountId=""></transaction-form>
      </div>

    </div>

  </div>

</div>

@stop