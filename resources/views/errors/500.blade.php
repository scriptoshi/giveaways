@extends('errors::minimal')

@section('title', __('Internal Server Error'))
@section('code', '500')
@section('message', __('Its not you, Something has failed from our end, our team has been notified and will address this at once.'))
