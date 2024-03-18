@extends('errors::minimal')

@section('title', __('Unauthorized Access'))
@section('code', '401')
@section('message', __('Whhooopsie , seem you dont have the required authorization to access this resource'))
