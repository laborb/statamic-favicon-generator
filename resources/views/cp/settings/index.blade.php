@extends('statamic::layout')
@section('title', __('favicon-generator::cp.general.headline'))

@section('content')
    <favicon-generator
        title="@lang('favicon-generator::cp.general.headline')"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :initial-values='@json($values)'
    ></favicon-generator>

    @include('statamic::partials.docs-callout', [
		'topic' => 'Favicon Generator',
		'url' => 'https://laborb.de'
	])
@endsection