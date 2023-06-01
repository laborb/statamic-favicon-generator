@extends('statamic::layout')
@section('title', __('statamic-favicon-generator::cp.general.headline'))

@section('content')
    <favicon-generator
        title="@lang('statamic-favicon-generator::cp.general.headline')"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :initial-values='@json($values)'
        generate="@lang('statamic-favicon-generator::cp.general.generate')"
    ></favicon-generator>

    @include('statamic::partials.docs-callout', [
		'topic' => 'Favicon Generator',
		'url' => 'https://statamic.com/addons/laborb/favicon-generator'
	])
@endsection

<style>
    .animate-spin {
        animation-name: spin;
        animation-duration: 1s;
        animation-iteration-count: infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>