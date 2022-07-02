@extends('principal')

@section('contenido')
    @if(Auth::check())
        @if (Auth::user()->idrol == 1)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>
            <template v-if="menu==1">
                <user></user>
            </template>
            <template v-if="menu==2">
                <vehiculo></vehiculo>
            </template>
            <template v-if="menu==3">
                <!-- <listarvehiculo></listarvehiculo> -->
            </template>
            <template v-if="menu==10">
                <logoCentral></logoCentral>
            </template>

        @elseif (Auth::user()->idrol == 2)
        @else
        @endif
    @endif
@endsection
