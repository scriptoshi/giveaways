@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('You are sending too many request at once, please wait a few moments and retry'))
