@extends('layouts.app')

@section('content')
<v-app>
    <v-content>
        <div id="app">
            <router-view />
        </div>
    </v-content>
</v-app>
@endsection