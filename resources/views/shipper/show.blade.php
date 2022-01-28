<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h2 class="modal-title" id="modalTitle"> @lang('shipper.details')
      </h2>
   </div>
    <div class="modal-body">

        <div class="row">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>{{ __('id') }}</th>
              <th>{{ __('shipper.name') }}</th>
              <th>{{ __('shipper.type') }}</th>
              <th>{{ __('shipper.tel') }}</th>
              <th>{{ __('shipper.other_details') }}</th>
            </tr>  
            <tr>
                <td>{{$shipper->id}}</td>
                <td>{{$shipper->shipper_name}}</td>
                <td>{{$shipper->type}}</td>
                <td>{{$shipper->tel}}</td>
                <td>{{$shipper->other_details}}</td> 
            </tr>
          </table> 
       </div> 
      </div>
    </div>  
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>
