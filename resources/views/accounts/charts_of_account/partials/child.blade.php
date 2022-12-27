@foreach($childs as $child)
    @php
        $i = session()->get('i');
        $i = $i + 1;
    @endphp
    <tr class="treegrid-{{ $i }} treegrid-parent-{{ $parent_id }}">
        <td>
            {{ $child->name }}
        </td>
        <td>{{ isset($child->alias) ? $child->alias : null }}</td>

        @if(backpack_user()->isSystemUser())
            <td>{{ isset($child->sup_org_id) ? $organization[$child->sup_org_id] : null }}</th>
            <td>{{ isset($child->store_id) ? $store[$child->store_id] : null }}</td>
        @elseif(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null)
            <td>{{ isset($child->store_id) ? $store[$child->store_id] : null }}</td>
        @endif

        <td>{{ isset($child->opening_balance) ? ($child->dr_cr == 0 ? 'Dr.' : 'Cr.') . ' ' .$child->opening_balance : '-' }}</td>
        <td>
            <!-- edit button -->
            @if(backpack_user()->isStoreUser() && isset($child->sup_org_id) && $child->store_id == null)
            @elseif($child->is_group == true && $child->sup_org_id != 1)
                <a data-fancybox data-type="ajax" data-src="{{ route('getGroupInfo', $child->id) }}" class="btn btn-sm btn-success" href="javascript:;" data-toggle="tooltip" title="Edit"><i class="la la-edit"></i></a>
            @elseif($child->is_ledger == true && $child->sup_org_id != 1)
                <a href="{{ url($crud->route.'/'.$child->id.'/edit') }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Edit"><i class="la la-edit"></i></a>
            @else
            @endif

            <!-- delete button -->
            @if(backpack_user()->isStoreUser() && isset($child->sup_org_id) && $child->store_id == null)
            @elseif((count($child->childs) > 0) || (count($child->subLedgers) > 0))
            @elseif((!(count($child->childs) > 0) || !(count($child->subLedgers) > 0)) && $child->sup_org_id != 1)
                <button class="btn btn-sm btn-danger" onclick="deleteCoa('{{ $child->id }}')" data-toggle="tooltip" title="Delete"><i class="la la-trash"></i></button>
            @endif
        </td>
        @if(count($child->subLedgers))
            {{ session()->put('i', $i) }}
            @include('accounts.charts_of_account.partials.child', ['childs' => $child->subLedgers, 'parent_id' => $i])
        @elseif(count($child->childs))
            {{ session()->put('i', $i) }}
            @include('accounts.charts_of_account.partials.child', ['childs' => $child->childs, 'parent_id' => $i])
        @else
            {{ session()->put('i', $i) }}
        @endif
    </tr>
@endforeach
