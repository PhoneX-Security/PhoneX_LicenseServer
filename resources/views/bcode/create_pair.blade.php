@extends('form.form-create')
@section('title', "New code pairs export")
@section('form')
    <form  role="form" method="POST" action="/bcodes/generate-code-pairs">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <h4>Code pair</h4>
        <p>Specify number of pairs you want to generate and license details. After both codes are used, newly created users are automatically connected via their contact lists.</p>

        <?php $numberDesc  = 'Number of pairs'; ?>
        @include('bcode.chips.new-codes-form', compact('groups', 'licenseTypes', 'licenseFuncTypes', 'numberDesc'))

    </form>
@endsection