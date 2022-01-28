@extends('layouts.app')

@section('title', __('shipper.add'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('shipper.add')
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
    @component('components.filters', ['title' => __('report.filters')])
        <div class="container-fluid">
    {!! Form::open(['route' => 'shipper.store']) !!}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('shipper_name', __('shipper.name') . '*:') !!}
                {!! Form::text('shipper_name', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('type', __('shipper.type') . '*:') !!}
                {!! Form::text('type', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('tel', __('shipper.tel') . '*:') !!}
                {!! Form::text('tel', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('other_details', __('shipper.other_details') . ':') !!}
                {!! Form::text('other_details', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
       
<!-- Submit Button -->
<div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {!! Form::submit(__('messages.add') , ['class' => 'btn btn-lg btn-info pull-right'] ) !!}
            </div>
        </div>

        {!! Form::close()  !!}
    </div>
        
    
    @endcomponent
    </div>
</div>



</section>
<!-- /.content -->

@endsection

