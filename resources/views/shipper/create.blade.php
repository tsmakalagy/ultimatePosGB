@extends('layouts.app')

@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('create shipper')
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <div class="">
    @component('components.filters', ['title' => __('report.filters')])
        
    {!! Form::open(['route' => 'shipper.store']) !!}
        <div class="">
            <div class="form-group">
                {!! Form::label('name', __('name') . ':') !!}
                {!! Form::text('name', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>

        <div class="">
            <div class="form-group">
                {!! Form::label('type', __('type') . ':') !!}
                {!! Form::text('type', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="">
            <div class="form-group">
                {!! Form::label('tel', __('tel') . ':') !!}
                {!! Form::text('tel', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="">
            <div class="form-group">
                {!! Form::label('other_details', __('other_details') . ':') !!}
                {!! Form::text('other_details', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
       
<!-- Submit Button -->
<div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {!! Form::submit('add', ['class' => 'btn btn-lg btn-info pull-right'] ) !!}
            </div>
        </div>

        {!! Form::close()  !!}

        
    
    @endcomponent
    </div>
</div>



</section>
<!-- /.content -->

@endsection

