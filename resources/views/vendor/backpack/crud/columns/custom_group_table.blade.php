@inject('master_data_array', 'App\Http\Controllers\MasterArrayDataController')

@php
	$columns = $column['columns'];
    
    foreach($columns as $column_key => $column){       
        $values = json_decode($entry->$column_key);
        foreach($values as $values_key => $value){
            $datas[$values_key][$column_key] = $value;
        }
    }
@endphp

<style>
    td, tr{
        line-height: 0em !important;
    }
    #crudTable_wrapper #crudTable tr td:first-child, #crudTable_wrapper #crudTable tr th:first-child{
        display: table-cell;
    }
    #crudTable_wrapper #crudTable td{
        vertical-align: unset;
    }
</style>
<span>
    @if($datas)
        <table class="table">
            <thead>
                <tr>
                    @foreach($columns as $tableColumnKey => $tableColumnLabel)
                        <th>{{ $tableColumnLabel }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $key => $data)
                    <tr>
                        @foreach($columns as $key => $column)
                            @if(is_array($data[$key]))
                                <td>
                                    <table class="table">
                                        @foreach($data[$key] as $data)
                                            <tr>
                                                @foreach($master_data_array->$key() as $k => $val)
                                                    @php 
                                                        if(intval($master_data_array->$key()[$k]['id']) == intval($data)){
                                                            $value = $master_data_array->$key()[$k]['name'];
                                                        }
                                                    @endphp
                                                @endforeach
                                                <td>{{ $value }}</td>
                                            </tr> 
                                        @endforeach
                                    </table>
                                </td>
                            @else 
                                @php 
                                    $value = $master_data_array->$key()[intval($data[$key])]['name'];
                                @endphp
                                <td>{{ $value }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
	@endif
</span>
