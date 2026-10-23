@extends('errors::minimal')

@section('title', '送信が多すぎます')
@section('code', '429')
@section('message', '短時間に送信が続いたため、受付を一時的に止めています。しばらく時間をおいてから、もう一度お試しください。')
