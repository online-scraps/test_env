@php
    $fields = ['id', 'code', 'sup_org_id', 'store_id', 'is_active', 'is_super_data', 'created_by', 'updated_by', 'deleted_by', 'is_deleted', 'is_deleted', 'deleted_uq_code', 'created_at', 'updated_at', 'sup_data_id', 'is_consumed', 'lft', 'rgt', 'depth', 'icon_picker', 'display_order'];

    if (isset($entry->properties['attributes'])) {
        $attrData = json_encode(array_filter($entry->properties['attributes']), true);
        $attrData = json_decode($attrData, true);
        foreach ($attrData as $key => $value) {
            if (in_array($key, $fields)) {
                unset($attrData[$key]);
            }
        }
    } else {
        $attrData = [];
    }

    if (isset($entry->properties['old'])) {
        $oldData = json_encode(array_filter($entry->properties['old']), true);
        $oldData = json_decode($oldData, true);
        foreach ($oldData as $key => $value) {
            if (in_array($key, $fields)) {
                unset($oldData[$key]);
            }
        }
    } else {
        $oldData = [];
    }

@endphp

<!-- Button trigger modal -->
<button type="button" class="btn btn-success btn-sm" data-toggle="modal"
    data-target="#activityDetailModal-{{ $entry->id }}">
    <i class="la la-eye"></i>
</button>
<!-- Modal -->
<div class="modal fade" id="activityDetailModal-{{ $entry->id }}" data-backdrop="static" data-keyboard="false"
    tabindex="-1" aria-labelledby="activityDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title  text-center" id="activityDetailModalLabel">{{ Str::headline($entry->event) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-primary font-weight-bolder">Current Details</h5>
                        @forelse ($attrData as $key => $value)
                            <p class="text-darker">
                                <span class="font-weight-bolder">{{ Str::headline($key) }} : </span>
                                {{ $value }}
                            </p>
                        @empty
                            <h5 class="card-title">No Details Found</h5>
                        @endforelse
                        <hr>
                        <h5 class="card-title text-primary font-weight-bolder">Old Details</h5>
                        @forelse ($oldData as $key => $value)
                            <p class="text-darker">
                                <span class="font-weight-bolder">{{ Str::headline($key) }} : </span>
                                {{ $value }}
                            </p>
                        @empty
                            <h5 class="card-title">No Changes Found</h5>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.modal').appendTo('body');
</script>
