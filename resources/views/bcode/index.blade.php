@extends('content-with-header')

@section('title', 'Business codes')
@section('subtitle', 'Manage')

@section('content')
    @parent

    <section class="content">
        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Business codes</h3>
                <div class="box-tools">
                    {{--<a class="btn btn-sm btn-primary view-btn-create" href="/bcodes/generate-single-codes/">--}}
                        {{--<i class="fa fa-plus-circle"></i> New single codes--}}
                    {{--</a>--}}

                    <form class="form-inline inline-block" action="/bcodes" method="get">

                        <div class="input-group">
                            <input type="text" name="code" value="{{Request::get('code')}}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search by code">
                            <div class="input-group-btn">
                                <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <a class="btn btn-sm btn-primary view-btn-create" href="/bcodes/generate-code-pairs/">
                        <i class="fa fa-plus-circle"></i> New code pairs
                    </a>
                </div>
            </div>
            <div class="box-body">
                @include('bcode.chips.bcodes-table')
            </div>
            <div class="box-footer clearfix">
                <div class="pull-left">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        Total {{ $bcodes->total() }} entries</div>
                </div>

                <div class="pull-right">
                    {!! $bcodes->appends(Request::except('page'))->render() !!}
                </div>
            </div>
        </div>

    </section>
@endsection
