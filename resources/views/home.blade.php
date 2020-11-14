@extends('layouts.app')

@section('content')


    <div class="row">


        <div class="offset-md-1 col-md-10">
            <chat :auth-user="{{ json_encode(auth()->user()) }}" :threads="{{ $threads }}"></chat>
        </div>

    </div>

@endsection
