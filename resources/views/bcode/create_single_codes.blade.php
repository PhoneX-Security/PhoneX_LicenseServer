@extends('form.form-create')
@section('title', "New single codes export")
@section('form')
    <form  role="form" method="POST" action="/bcodes/generate-single-codes">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <h4>Single codes</h4>
        {{--<p></p>--}}

        @include('bcode.chips.new-codes-form', compact('groups', 'licenseTypes', 'licenseFuncTypes'))

    </form>
@endsection