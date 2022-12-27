@php
	$keyName = isset($column['key']) ? $column['key'] : $column['name'];
	$entryValue = data_get($entry, $keyName);

	if($entryValue == false){
		$displayValue ='<i class="fas fa-toggle-off" style="font-size:20px;color:red;"></i>';
	}else{
		$displayValue ='<i class="fas fa-toggle-on" style="font-size:20px;color:green;"></i>';
	}
@endphp

<span>
	{!! $displayValue !!}
</span>