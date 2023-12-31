<div class="modal fade modal-ok" tabindex="-1" role="dialog" id="{{ $id }}">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>{!! $msg !!}</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default -btn -btn-effect" data-dismiss="modal">{{ $okButtonText ?? 'OK'}}</button>
            </div>

        </div>
    </div>

</div>
