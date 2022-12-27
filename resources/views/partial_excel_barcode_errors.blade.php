@if (isset($barcode_errors))
@if (count($barcode_errors) > 0)
<h5>Following barcodes are alreday registered In the system</h5>
<table class="table table-sm mt-2">
    <thead>
        <tr>
            <th>S.N.</th>
            <th>Items</th>
            <th>Barcode</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barcode_errors as $key => $value)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $value }}</td>
            <td>{{ $key }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif

@if (isset($differeent_data_errors))
@if (!empty(array_filter($differeent_data_errors)))

<h4 class="text-center text-danger mx-5 text-bold">
    Following are the different value for Same Item
</h4>
<table class="table mt-2">
    <thead>
        <tr>
            <th class="text-center">S.N.</th>
            <th class="text-center">Error</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 1;
        @endphp
        @foreach ($differeent_data_errors as $items)
        @foreach ($items as $error)

        {{-- {{ dd($error) }} --}}
        <tr>
            <td class="text-center">{{ $i++ . '. ' }}</td>
            <td class="text-center">{!! $error !!}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

@endif
@endif


@if (isset($item_errors))
@if (!empty(array_filter($item_errors)))
<h4 class="text-center text-danger mx-5 text-bold">
    Errors
</h4>
<table class="table mt-2">
    <thead>
        <tr>
            <th class="text-center">S.N.</th>
            <th class="text-center">Error</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 1;
        @endphp
        @foreach ($item_errors as $key => $items)
        @foreach ($items as $key => $error)
        <tr>
            <td class="text-center">{{ $i++ . '. ' }}</td>
            <td class="text-center">{!! $error !!}</td>
        </tr>
        @endforeach

        @endforeach
    </tbody>
</table>
@endif
@endif

@if (isset($name_errors))
@if (!empty(array_filter($name_errors)))
<h4 class="text-center text-danger mx-5 text-bold">
    Invalid Data
</h4>
<table class="table mt-2">
    <thead>
        <tr>
            <th class="text-center">S.N.</th>
            <th class="text-center">Error</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 1;
        @endphp
        @foreach ($name_errors as $key => $items)
        @foreach ($items as $key => $error)
        <tr>
            <td class="text-center">{{ $i++ . '. ' }}</td>
            <td class="text-center">{!! $error !!}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
@endif
@endif

@if (isset($database_validation_errors))
@if (!empty(array_filter($database_validation_errors)))


<h4 class="text-center text-danger mx-5 text-bold">
    Validation Errors
</h4>
<table class="table mt-2">
    <thead>
        <tr>
            <th class="text-center">S.N.</th>
            <th class="text-center">Error</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 1;
        @endphp
        @foreach ($database_validation_errors as $items)
        @foreach ($items->errors() as $error)
        <tr>
            <td class="text-center">{{ $i++ . '. ' }}</td>
            <td class="text-center">{!! $error.' on row '. $items->row() !!}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

@endif

@endif