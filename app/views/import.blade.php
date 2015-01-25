@extends('layouts.default')
@section('content')
<div>
  <?php print_r(Session::get('message')); ?>
  {{ Form::open( array('action' => 'Feenance\controllers\Api\TransactionsController@upload', 'class'=>'form-horizontal', 'files' => true)) }}
    <account-selector ng-model="account" account-id="2" name="account_id"> </account-selector>
    {{Form::file('file')}}
    {{ Form::submit('Submit', array('class' => 'btn btn-info')) }}

  {{ Form::close() }}

</div>
@stop