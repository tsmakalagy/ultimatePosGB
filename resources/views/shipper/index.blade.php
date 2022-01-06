@extends('layouts.app')

@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.products')
        <small>@lang('lang_v1.manage_products')</small>
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
        
    {!! Form::open(['route' => 'shipper.store', 'class' => 'form-horizontal']) !!}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('name', __('name') . ':') !!}
                {!! Form::text('name', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type', __('type') . ':') !!}
                {!! Form::text('type', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('tel', __('tel') . ':') !!}
                {!! Form::text('tel', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('other_details', __('other_details') . ':') !!}
                {!! Form::text('other_details', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
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




<table class="table table-bordered table-striped ajax_view hide-footer" id="product_table">
    <thead>
        <tr>
            
            <th>@lang('name')</th>
            <th>@lang('type')</th>
            <th>@lang('tel')</th>
            <th>@lang('other_details')</th>
 
            
        </tr>
    </thead>
    <tbody>
        @foreach($shipper as $shippers)
        <tr>
            <td>{{$shippers->name}}</td>
            <td>{{$shippers->type}}</td>
            <td>{{$shippers->tel}}</td>
            <td>{{$shippers->other_details}}</td>
            <th><a href="{{action('ShipperController@edit',['id'=>$shippers->id])}}">@lang('edit')</a></th>
            <th><a href="{{action('ShipperController@delete',['id'=>$shippers->id])}}">@lang('delete')</a></th>
        </tr>
        @endforeach
    </tbody>
    
</table>

</section>
<!-- /.content -->

@endsection

