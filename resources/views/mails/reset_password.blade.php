@extends('layouts.email')

@section('content')
<div>
    <p>{{trans('mails.reset_password')}}</p>
    <p>{{$token}}
</div>
@endsection