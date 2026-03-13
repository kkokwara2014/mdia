@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('adminlte_css_pre')
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
@stop

@section('content_header')
    <h1>@yield('page_title')</h1>
@stop

@section('content')
    @yield('page_content')
@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/mdia-theme.css') }}">
@stop

@section('css')
    @yield('custom_css')
@stop

@section('js')
    @yield('custom_js')
@stop
