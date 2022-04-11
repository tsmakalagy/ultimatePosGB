@extends('layouts.app')

@section('title', __('lang_v1.product_price_setting.add'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.product_price_setting.add')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
       
<style>

    .stretch-card>.card {
        width: 100%;
        min-width: 100%
    }
    
    
    .flex {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto
    }
    
   
    
  
    
    .card {
        box-shadow: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        -ms-box-shadow: none
    }
    
    .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #3da5f;
        border-radius: 0
    }
    
    .card .card-body {
        padding: 1.25rem 1.75rem
    }
    
    .card .card-title {
        color: #000000;
        margin-bottom: 0.625rem;
        text-transform: capitalize;
        font-size: 0.875rem;
        font-weight: 500
    }
    
    .card .card-description {
        margin-bottom: .875rem;
        font-weight: 400;
        color: #76838f
    }
    
  
  
    
    .editable-form .editable-click {
        border-color: #000
    }
    
    .editable-container.editable-inline {
        max-width: 100%
    }
    
    .editable-container.editable-inline .editableform {
        max-width: 100%
    }
    
    .editable-container.editable-inline .editableform .control-group {
        max-width: 100%;
        white-space: initial
    }
    
    .editable-container.editable-inline .editableform .control-group>div {
        max-width: 100%
    }
    
    .editable-container.editable-inline .editableform .control-group .editable-input input,
    .editable-container.editable-inline .editableform .control-group .editable-input textarea {
        max-width: 100%;
        width: 100%
    }
    
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .form-control,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .asColorPicker-input,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .dataTables_wrapper select,
    .dataTables_wrapper .editable-container.editable-inline .editableform .control-group .editable-input .combodate select,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .jsgrid .jsgrid-table .jsgrid-filter-row input[type=text],
    .jsgrid .jsgrid-table .jsgrid-filter-row .editable-container.editable-inline .editableform .control-group .editable-input .combodate input[type=text],
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .jsgrid .jsgrid-table .jsgrid-filter-row select,
    .jsgrid .jsgrid-table .jsgrid-filter-row .editable-container.editable-inline .editableform .control-group .editable-input .combodate select,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .jsgrid .jsgrid-table .jsgrid-filter-row input[type=number],
    .jsgrid .jsgrid-table .jsgrid-filter-row .editable-container.editable-inline .editableform .control-group .editable-input .combodate input[type=number],
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .select2-container--default .select2-selection--single,
    .select2-container--default .editable-container.editable-inline .editableform .control-group .editable-input .combodate .select2-selection--single,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .select2-container--default .select2-selection--single .select2-search__field,
    .select2-container--default .select2-selection--single .editable-container.editable-inline .editableform .control-group .editable-input .combodate .select2-search__field,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .typeahead,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .tt-query,
    .editable-container.editable-inline .editableform .control-group .editable-input .combodate .tt-hint {
        padding-left: 0;
        padding-right: 0
    }
    
    @media (max-width: 991px) {
        .editable-container.editable-inline .editableform .control-group .editable-buttons {
            display: block;
            margin-top: 10px
        }
    }
    
    .btn-group-sm>.btn,
    .btn-sm {
        padding: 0.49rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem
    }
    
    .btn-warning {
        color: #fff
    }</style>
    </section>

    <!-- Main content -->
    <section class="content">

       
        <div class="page-content page-container" id="page-content">
            <div class="padding">
                <div class="row container d-flex justify-content-center">
                    <div class="col-lg-7 grid-margin stretch-card">
                        <!--x-editable starts-->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">X-editable Editor</h4>
                                <p class="card-description">Edit forms inline(click on the underline text to test below)</p>
                                <div class="template-demo">
                                    <form id="editable-form" class="editable-form">
                                        <div class="form-group row"> <label class="col-6 col-lg-4 col-form-label">Simple text field</label>
                                            <div class="col-6 col-lg-8 d-flex align-items-center"> <a href="#" id="username" data-type="text" data-pk="1" class="editable editable-click" data-abc="true" style="">awesome</a> </div>
                                        </div>
                                        <div class="form-group row"> <label class="col-6 col-lg-4 col-form-label">Empty text field, required</label>
                                            <div class="col-6 col-lg-8 d-flex align-items-center"> <a href="#" id="firstname" data-type="text" data-pk="1" data-placement="right" data-placeholder="Required" data-title="Enter your firstname" class="editable editable-click editable-empty" data-abc="true">Empty</a> </div>
                                        </div>
                                        <div class="form-group row"> <label class="col-6 col-lg-4 col-form-label">Select, local array, custom display</label>
                                            <div class="col-6 col-lg-8 d-flex align-items-center"> <a href="#" id="sex" data-type="select" data-pk="1" data-value="" data-title="Select sex" class="editable editable-click" data-abc="true">not selected</a> </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--x-editable ends-->
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        {!! Form::open(['url' => action('ProductPriceSettingController@store'), 'method' => 'post', 'id' => 'price_product_add_form']) !!}

                        <input id="hide_id" name="invisible" type="hidden" value="{{$product_price_setting->id}}">
                        <input id="hide_cu" name="invisible" type="hidden" value="{{$product_price_setting->cours_usd}}">
                        <input id="hide_fub" name="invisible" type="hidden" value="{{$product_price_setting->frais_usd_bateau}}">
                        <input id="hide_cr" name="invisible" type="hidden" value="{{$product_price_setting->cours_rmb}}">
                        <input id="hide_ct" name="invisible" type="hidden" value="{{$product_price_setting->constante_taxe}}">
                        <input id="hide_fcub" name="invisible" type="hidden" value="{{$product_price_setting->frais_compagnie_usd_bateau}}">
                        <input id="fret" name="invisible" type="hidden" value="">
                        <input id="taxe" name="invisible" type="hidden" value="">
   
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_usd', __('lang_v1.cours_usd') . '*:') !!}
                                {!! Form::text('cours_usd', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_rmb', __('lang_v1.cours_rmb') . '*:') !!}
                                {!! Form::text('cours_rmb', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_bateau', __('lang_v1.frais_taxe_usd_bateau') . '*:') !!}
                                {!! Form::text('frais_taxe_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_avion', __('lang_v1.frais_taxe_usd_avion') . ':') !!}
                                {!! Form::text('frais_taxe_usd_avion', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_usd_bateau', __('lang_v1.frais_usd_bateau') . ':') !!}
                                {!! Form::text('frais_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_compagnie_usd_bateau', __('lang_v1.frais_compagnie_usd_bateau') . ':') !!}
                                {!! Form::text('frais_compagnie_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('constante_taxe', __('lang_v1.constante_taxe') . ':') !!}
                                {!! Form::text('constante_taxe', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.add') , ['class' => 'btn btn-primary pull-right'] ) !!}
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script type="text/javascript">
   // $(document).ready(function(){
     //    $("#mobile").inputmask({"mask": "(999) 99 999 99"});

          
  $("#mobile").blur(function(){
      var error_tel='';
      var mobile= $("#mobile").val();
      var form = $("form#contact_add_form");
      var url = form.attr('action');
      $.ajax({
          url:"/validate_mobile/check",
          method:"POST",
          
          data:{mobile:mobile},
          success:function(result){
              if (result =='unique'){

                  $("#error_tel").html('<label class="text-success"></label>');
              }
              else{
                $("#error_tel").html('<label class="text-danger">tel not available</label>');
                  
              }
          }
      })
  });

      });
  </script>
<script type="text/javascript">

  (function($) {
'use strict';
$(function() {
if ($('#editable-form').length) {
$.fn.editable.defaults.mode = 'inline';
$.fn.editableform.buttons =
'<button type="submit" class="btn btn-primary btn-sm editable-submit">' +
    '<i class="fa fa-fw fa-check"></i>' +
    '</button>' +
'<button type="button" class="btn btn-warning btn-sm editable-cancel">' +
    '<i class="fa fa-fw fa-times"></i>' +
    '</button>';
$('#username').editable({
type: 'text',
pk: 1,
name: 'username',
title: 'Enter username'
});

$('#firstname').editable({
validate: function(value) {
if ($.trim(value) === '') return 'This field is required';
}
});

$('#sex').editable({
source: [{
value: 1,
text: 'Male'
},
{
value: 2,
text: 'Female'
}
]
});

$('#status').editable();

$('#group').editable({
showbuttons: false
});

$('#vacation').editable({
datepicker: {
todayBtn: 'linked'
}
});

$('#dob').editable();

$('#event').editable({
placement: 'right',
combodate: {
firstItem: 'name'
}
});

$('#meeting_start').editable({
format: 'yyyy-mm-dd hh:ii',
viewformat: 'dd/mm/yyyy hh:ii',
validate: function(v) {
if (v && v.getDate() === 10) return 'Day cant be 10!';
},
datetimepicker: {
todayBtn: 'linked',
weekStart: 1
}
});

$('#comments').editable({
showbuttons: 'bottom'
});

$('#note').editable();
$('#pencil').on("click", function(e) {
e.stopPropagation();
e.preventDefault();
$('#note').editable('toggle');
});

$('#state').editable({
source: ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Dakota", "North Carolina", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"]
});

$('#state2').editable({
value: 'California',
typeahead: {
name: 'state',
local: ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Dakota", "North Carolina", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"]
}
});

$('#fruits').editable({
pk: 1,
limit: 3,
source: [{
value: 1,
text: 'banana'
},
{
value: 2,
text: 'peach'
},
{
value: 3,
text: 'apple'
},
{
value: 4,
text: 'watermelon'
},
{
value: 5,
text: 'orange'
}
]
});

$('#tags').editable({
inputclass: 'input-large',
select2: {
tags: ['html', 'javascript', 'css', 'ajax'],
tokenSeparators: [",", " "]
}
});

$('#address').editable({
url: '/post',
value: {
city: "Moscow",
street: "Lenina",
building: "12"
},
validate: function(value) {
if (value.city === '') return 'city is required!';
},
display: function(value) {
if (!value) {
$(this).empty();
return;
}
var html = '<b>' + $('<div>').text(value.city).html() + '</b>, ' + $('<div>').text(value.street).html() + ' st., bld. ' + $('<div>').text(value.building).html();
        $(this).html(html);
        }
        });

        $('#user .editable').on('hidden', function(e, reason) {
        if (reason === 'save' || reason === 'nochange') {
        var $next = $(this).closest('tr').next().find('.editable');
        if ($('#autoopen').is(':checked')) {
        setTimeout(function() {
        $next.editable('show');
        }, 300);
        } else {
        $next.focus();
        }
        }
        });
        }
        });
        })(jQuery);


  </script>


<script type="text/javascript">
    $(document).ready(function(){
      
   

      $("#frais_taxe_usd_avion").blur(function() {
       var frais_taxe_usd_avion = $(this).val();
       var hide1 = $('#hide_id').val();
        var cu = $('#hide_cu').val();
         var cr = $('#hide_cr').val();
          var ct = $('#hide_ct').val();
           var fcub = $('#hide_fcub').val();
            var fub = $('#hide_fub').val();
            var fret=frais_taxe_usd_avion*cu*fub;

            $('#fret').val(fret);
        }); 

       
        $("#frais_taxe_usd_bateau").blur(function() {
       var frais_taxe_usd_bateau = $(this).val();
       var frais_taxe_usd_avion = $('#frais_taxe_usd_avion').val();
        var cu = $('#hide_cu').val();
         var cr = $('#hide_cr').val();
          var ct = $('#hide_ct').val();
           var fcub = $('#hide_fcub').val();
            var fub = $('#hide_fub').val();
            var fret= $('#fret').val();
            var taxe=(frais_taxe_usd_bateau*cr*(ct+frais_taxe_usd_avion)*cu*fcub)*0.5;

            $('#taxe').val(taxe);
        }); 
        
        $("#cours_usd").blur(function() {
            var fret= $('#fret').val();
            var taxe= $('#taxe').val();
    //alert(taxe);
        }); 
 /*
 Fret = volume*cours_usd*fret_usd_bateau
 taxe=(prix_chine*cours_rmb*constante_taxe+volume*cours_usd*fret_company_usd_bateau)*0.5
 */

 //suggested price
		/*$.ajax({
        type: 'GET',
        url: '/sells/create',
        data: {selectedbrand:selectedbrand},
        success: function(response) {
		$("option").remove(".theOption");
        var text = "";
        var i;
        for (i = 0; i < response.length; i++) {
           
           text+='<option value="'+response[i].id+'" class="theOption">'+response[i].nom+'</option>';
    
        }  $("#address_id").append(text);
        }
    });*/
 
    



    
          
          $("form#shipper_add_form").validate({
              
              submitHandler: function (form) {
                
                var form = $("form#shipper_add_form");
                var url = form.attr('action');
                
                form.find('button[type="submit"]').attr('disabled', true);
                $.ajax({
                    method: "POST",
                    url: url,
                    dataType: 'json',
                    data: $(form).serialize(),
                    success: function(data){
                        $('.shipper_modal').modal('hide');
                        if( data.success){
                            toastr.success(data.msg);
                           
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                });
                return true;
              }
            });
          
          
          });
          
              </script>







