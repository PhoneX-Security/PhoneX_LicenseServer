<div class="modal fade" id="modal-reset-trial-counter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                Do you want to reset trial counter?
            </div>
            {!! \Form::open(['method' => 'post', 'route' => ['users.reset-trial-counter', $user->id]]) !!}
            <div class="modal-body">
                <p>User will be able to create new trial account with different name on the same IMEI</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-ok">Yes</button>
            </div>
            {!! \Form::close() !!}
        </div>
    </div>
</div>
