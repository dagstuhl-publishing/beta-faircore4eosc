<div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true close-btn">×</span>

                </button>

            </div>

            <div class="modal-body">

                <p>Are you sure want to clear fields?</p>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-info -btn -btn-effect close-btn" data-dismiss="modal">Close</button>

                <button type="button" wire:click.prevent="remove()" class="btn btn-danger -btn -btn-effect-erase  close-modal" data-dismiss="modal">Yes, Delete</button>

            </div>

        </div>

    </div>

</div>
