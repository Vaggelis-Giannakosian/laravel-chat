@extends('layouts.app')

@section('content')


    <div class="row">


        <div class="offset-md-2 col-md-6">
            <chat :auth-user="{{ json_encode(auth()->user()) }}" receiver-id="{{ auth()->id() == '1' ? '2' : '1' }}"></chat>
        </div>

    </div>

@endsection
