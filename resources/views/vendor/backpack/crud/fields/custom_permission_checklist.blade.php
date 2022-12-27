<!-- checklist -->
@php
  $model = new $field['model'];
  $key_attribute = $model->getKeyName();
  $identifiable_attribute = $field['attribute'];

  // calculate the checklist options
  if (!isset($field['options'])) {
    $field['options'] = $field['model']::all()->pluck($identifiable_attribute, $key_attribute)->toArray();
  } else {
    $field['options'] =$field['options']->pluck($identifiable_attribute, $key_attribute)->toArray();
  }

  // calculate the value of the hidden input
  $field['value'] = old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '';
  if ($field['value'] instanceof \Illuminate\Support\Collection) {
    $field['value'] = $field['value']->pluck($key_attribute)->toJson();
  }

  // define the init-function on the wrapper
  $field['wrapper']['data-init-function'] =  $field['wrapper']['data-init-function'] ?? 'bpFieldInitChecklist';


  $permission_collection= [];
  foreach($field['options'] as $key=>$name){
    $entity_arr = explode(' ',$name);
    $permission_collection[end($entity_arr)][$key] = $entity_arr[0];
  }
@endphp
@include('crud::fields.inc.wrapper_start')
<hr/>
    <label>{!! $field['label'] !!}</label>
    <div class="row">
      <div class="col-md-3"><span>All Permission</span></div>
        <div class="col-sm-1" style="cursor: pointer !important;">
          <div class="checkbox">
          <label class="font-weight-normal" style="cursor: pointer">
              <input type="checkbox" id="custom_permission_checklist_all"  onclick="checkAllPermissions('{{json_encode($permission_collection)}}')"> All
          </label>
          </div>
        </div>

          <div class="col-sm-2" style="cursor: pointer !important;">
              <div class="checkbox">All
              <label class="font-weight-normal" style="cursor: pointer">
                  <input type="checkbox" id="custom_permission_checklist_list" onclick="checkAllPermissions('{{json_encode($permission_collection)}}')"> 
              </label>
              </div>
          </div>
          <div class="col-sm-2" style="cursor: pointer !important;">
              <div class="checkbox">All
              <label class="font-weight-normal" style="cursor: pointer">
                  <input type="checkbox" id="custom_permission_checklist_create" onclick="checkAllPermissions('{{json_encode($permission_collection)}}')"> 
              </label>
              </div>
          </div>
          <div class="col-sm-2" style="cursor: pointer !important;">
              <div class="checkbox">All
              <label class="font-weight-normal" style="cursor: pointer">
                  <input type="checkbox" id="custom_permission_checklist_update" onclick="checkAllPermissions('{{json_encode($permission_collection)}}')"> 
              </label>
              </div>
          </div>
          <div class="col-sm-2" style="cursor: pointer !important;">
              <div class="checkbox">All
              <label class="font-weight-normal" style="cursor: pointer">
                  <input type="checkbox" id="custom_permission_checklist_delete" onclick="checkAllPermissions('{{json_encode($permission_collection)}}')"> 
              </label>
              </div>
          </div>
  </div>
    @include('crud::fields.inc.translatable_icon')
    <input type="hidden" value="{{$field['value']}}" name="{{ $field['name'] }}">
    @foreach($permission_collection as $key=>$collection)
      <div class="row">
        <div class="col-md-3"><span>{{$key}}</span></div>
          <div class="col-sm-1" style="cursor: pointer !important;">
            <div class="checkbox">
            <label class="font-weight-normal" style="cursor: pointer">
                <input type="checkbox" id="{{$key}}" value onclick="checkall('{{$key}}','{{json_encode($collection)}}')"> All
            </label>
            </div>
          </div>

          @foreach($collection as $key=>$option)
            <div class="col-sm-2" style="cursor: pointer !important;">
                <div class="checkbox">
                <label class="font-weight-normal" style="cursor: pointer">
                    <input type="checkbox" value="{{ $key }}"> {{ $option }}
                </label>
                </div>
            </div>
        @endforeach
    </div>

    @endforeach
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <script>
          var checkboxes;
          var hidden_input

            function bpFieldInitChecklist(element) {
                hidden_input = element.find('input[type=hidden]');
                var selected_options = JSON.parse(hidden_input.val() || '[]');
                checkboxes = element.find('input[type=checkbox]');
                var container = element.find('.row');
                let all_checked = element.find('#custom_permission_checklist_all');

                // set the default checked/unchecked states on checklist options
                checkboxes.each(function(key, option) {
                  var id = parseInt($(this).val());

                  if (selected_options.includes(id)) {
                    $(this).prop('checked', 'checked');
                  } else {
                    $(this).prop('checked', false);
                  }
                });


                // when a checkbox is clicked
                // set the correct value on the hidden input

                checkboxes.click(function() {
                  var newValue = [];

                  checkboxes.each(function() {
                    if ($(this).is(':checked') && $(this).val() != '' && $(this).val() !== 'on') {
                      var id = $(this).val();
                      newValue.push(id);
                    }
                  });


                  hidden_input.val(JSON.stringify(newValue));
                  console.log(newValue)

                });
            }

            function checkAllPermissions(permission_collection){
              var collections = JSON.parse(permission_collection)
              $.each(collections, function(key, collection){
                checkAllPermission(key,collection)
                })
                setTimeout(() => {
                  getCheckedBoxIds();
                }, 1000);

            }
            function getCheckedBoxIds()
            {
              var temp_new_values=[]

                  $('input[type=checkbox]').each(function() {
                    if ($(this).is(':checked') && $(this).val() != '' && $(this).val() !== 'on') {
                      var id = $(this).val();
                      temp_new_values.push(id);
                    }
                  });
                  hidden_input.val(JSON.stringify(temp_new_values));

              console.log(temp_new_values);
            }

            function checkAllPermission(key,collection){
              // Checkboxes All
              $("#custom_permission_checklist_all").click(function() {
                if (this.id === "custom_permission_checklist_all") {
                  
                  $.each( collection, function(key, val ) {
                    $('input[value='+key+']').prop('checked',  'checked');
                  });
                }
                if(this.id === "custom_permission_checklist_all" && !this.checked){
                  $.each( collection, function( key ) {
                    $('input[value='+key+']').prop('checked', false);
                  });
                }
             });
  
              // Checkboxes List
              $("#custom_permission_checklist_list").on("change", function() {
                if (this.id === "custom_permission_checklist_list" && this.checked) {
                  $.each( collection, function( key, val ) {
                    if(val == 'list'){
                      $('input[value='+key+']').prop('checked',  'checked');
                    }
                  });
                }
                if(this.id === "custom_permission_checklist_list" && !this.checked){
                  $.each( collection, function( key, val ) {
                    if(val == 'list'){
                      $('input[value='+key+']').prop('checked', false);
                    }
                  });
                }
              });
              // Checkboxes Create
              $("#custom_permission_checklist_create").on("change", function() {
                if (this.id === "custom_permission_checklist_create" && this.checked) {
                  $.each( collection, function( key, val ) {
                    if(val == 'create'){
                      $('input[value='+key+']').prop('checked',  'checked');
                    }
                  });
                }
                if(this.id === "custom_permission_checklist_create" && !this.checked){
                  $.each( collection, function( key, val ) {
                    if(val == 'create'){
                      $('input[value='+key+']').prop('checked', false);
                    }
                  });
                }
              });
              // Checkboxes Update
              $("#custom_permission_checklist_update").on("change", function() {
                if (this.id === "custom_permission_checklist_update" && this.checked) {
                  $.each( collection, function( key, val ) {
                    if(val == 'update'){
                      $('input[value='+key+']').prop('checked',  'checked');
                    }
                  });
                }
                if(this.id === "custom_permission_checklist_update" && !this.checked){
                  $.each( collection, function( key, val ) {
                    if(val == 'update'){
                      $('input[value='+key+']').prop('checked', false);
                    }
                  });
                }
              });
              // Checkboxes Delete
              $("#custom_permission_checklist_delete").on("change", function() {
                if (this.id === "custom_permission_checklist_delete" && this.checked) {
                  $.each( collection, function( key, val ) {
                    if(val == 'delete'){
                      $('input[value='+key+']').prop('checked',  'checked');
                    }
                  });
                }
                if(this.id === "custom_permission_checklist_delete" && !this.checked){
                  $.each( collection, function( key, val ) {
                    if(val == 'delete'){
                      $('input[value='+key+']').prop('checked', false);
                    }
                  });
                }
              });
            }

            function checkall(key,collection){
              collection = JSON.parse(collection);
              if ($('#'+key).is(':checked')) {
                $.each( collection, function( key ) {
                  $('input[value='+key+']').prop('checked', 'checked');
                });
              }
              else{
                $.each( collection, function( key ) {
                  $('input[value='+key+']').prop('checked', false);
                });
              }
            }
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}