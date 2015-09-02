<div class="modal fade" id="modal-force-logout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                Force logout
            </div>
            {!! \Form::open(['method' => 'post', 'route' => ['users.force-logout', $user->id]]) !!}
            <div class="modal-body">
                <p>Do you want to force logout on all user's devices?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-ok">Yes</button>
            </div>
            {!! \Form::close() !!}
        </div>
    </div>
</div>
