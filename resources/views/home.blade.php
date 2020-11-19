@extends('layouts.app')

@section('content')


    <div class="row">


        <div class="offset-md-1 col-md-10">
            <chat :auth-user="{{ json_encode(auth()->user()) }}" :threads="{{ $threads }}"></chat>
        </div>

    </div>

    <div class="row">


{{--        <div class="offset-md-1 col-md-10">--}}
{{--           <pavla-beautiful-chat> </pavla-beautiful-chat>--}}
{{--        </div>--}}

        <div class="offset-md-2 col-md-3">
            <pavla-quick-chat> </pavla-quick-chat>
        </div>


    </div>


@endsection
