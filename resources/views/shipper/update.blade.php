@extends('layouts.app')

@section('title', __('shipper.edit_shipper'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('shipper.edit_shipper')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        {!! Form::open(['action' =>['ShipperController@update',$shipper->id]]) !!}
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('shipper_name', __('shipper.name') . ':*') !!}
                                {!! Form::text('shipper_name', $shipper->shipper_name , ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('type', __('shipper.type') . ':*') !!}
                                {!! Form::text('type', $shipper->type, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('tel', __('shipper.tel') . ':*') !!}
                                {!! Form::text('tel',$shipper->tel , ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('other_details', __('shipper.other_details') . ':') !!}
                                {!! Form::text('other_details', $shipper->other_details, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.update') , ['class' => 'btn btn-primary pull-right'] ) !!}
                            </div>
                        </div>

                        {!! Form::close()  !!}

                        <div>

                            @endcomponent
                        </div>
                    </div>


    </section>
    <!-- /.content -->

@endsection

