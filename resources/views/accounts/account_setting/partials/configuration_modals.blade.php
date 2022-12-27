<!-- image note configure modal start -->
<div class="modal fade" id="imageNoteConfigureModal" tabindex="-1" role="dialog" aria-labelledby="imageNoteConfigureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="imageNoteConfigureModalLabel">Options for Imanges and Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-5">
                @if(isset($image_note_configure))
                <form action="{{ route('updateImageNoteConfigure',$image_note_configure->id) }}" method="POST" id="imageNoteConfigureForm">
                @else 
                    <form action="{{ route('saveImageNoteConfigure') }}" method="POST" id="imageNoteConfigureForm">
                @endif
                    @csrf 
                    <div class="row">
                        <div class="col-12 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="image_with_account_master" id="image_with_account_master" {{ isset($image_note_configure) ? ($image_note_configure->image_with_account_master == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="image_with_account_master">Maintain Image with Account Master</label>
                        </div>

                        <div class="col-6 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="note_with_account_master" id="note_with_account_master" {{ isset($image_note_configure) ? ($image_note_configure->note_with_account_master == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="note_with_account_master">Maintain Note with Account Master</label>
                        </div>

                        <div class="col-6 form-group mb-3 from-inline">
                            <label class="left" for="account_master_char">Max. Char. in One Line</label>
                            <input type="number" class="form-control right width-50" name="account_master_char" id="account_master_char" value="{{ isset($image_note_configure) ? $image_note_configure->account_master_char : null }}" min="0">
                        </div>

                        <div class="col-12 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="account_note_in_data_entry" id="account_note_in_data_entry" {{ isset($image_note_configure) ? ($image_note_configure->account_note_in_data_entry == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="account_note_in_data_entry">Show Accoutn Note in Data Entry</label>
                        </div>

                        <div class="col-12 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="image_with_account_voucher" id="image_with_account_voucher" {{ isset($image_note_configure) ? ($image_note_configure->image_with_account_voucher == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="image_with_account_voucher">Maintain Image with Accounting Vouchers</label>
                        </div>

                        <div class="col-6 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="note_with_account_voucher" id="note_with_account_voucher" {{ isset($image_note_configure) ? ($image_note_configure->note_with_account_voucher == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="note_with_account_voucher">Maintain Note with Account Voucher</label>
                        </div>

                        <div class="col-6 form-group mb-3 from-inline">
                            <label class="left" for="account_voucher_char">Max. Char. in One Line</label>
                            <input type="number" class="form-control right width-50" name="account_voucher_char" id="account_voucher_char" value="{{ isset($image_note_configure) ? $image_note_configure->account_voucher_char : null }}" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- image note configure modal end -->