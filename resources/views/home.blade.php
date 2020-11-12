@extends('layouts.app')

@section('content')


    <div class="row">


        <div class="offset-md-2 col-md-6">
            <chat :auth-user="{{ json_encode(auth()->user()) }}" :threads="{{ json_encode(auth()->user()->threads) }}"></chat>
        </div>

    </div>

@endsection
