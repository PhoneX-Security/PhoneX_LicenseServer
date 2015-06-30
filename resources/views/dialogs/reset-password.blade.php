<div class="modal fade" id="reset-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                Reset SIP password
            </div>
            {!! \Form::open(['method' => 'patch', 'route' => ['users.change_password', $user->id]]) !!}
            <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">New password:</label>
                        <input type="text" class="form-control" id="recipient-name" name="password" value="{{ old('password','phonexxx') }}">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger btn-ok">Reset</button>
            </div>
            {!! \Form::close() !!}
        </div>
    </div>
</div>


<script>
</script>