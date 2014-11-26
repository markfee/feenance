@extends('layouts.default')
@section('content')
<div class="row">
    <div class="large-6 large-centered column">
            {{ Form::open(['action' => 'Feenance\Controllers\AdminController@login']) }}
            <fieldset>
                <legend>Login</legend>
                {{ Form::label('username','Username') }}
                {{ Form::text('username',Input::old('username'),['placeholder'=>'Your nice name']) }}
                {{ Form::label('password','Password') }}
                {{ Form::password('password',['placeholder'=>'Password here']) }}
                {{ Form::submit('Login',['class'=>'button tiny radius']) }}
            </fieldset>
            {{ Form::close() }}
            @if($errors->has())
                @foreach ($errors->all() as $message)
                    <span class="label alert round">{{$message}}</span><br><br>
                @endforeach
            @endif
            @if(Session::has('failure'))
                <span class="label alert round">{{Session::get('failure')}}</span>
            @endif

    </div>
</div>

@stop