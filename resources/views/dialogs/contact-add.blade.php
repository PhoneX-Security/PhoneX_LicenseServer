<div class="modal fade" id="modal-contact-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                Add contact
            </div>
            {!! \Form::open(['method' => 'patch', 'route' => ['users.add_user_to_cl', $user->id]]) !!}
            <div class="modal-body">

                    <div class="form-group">
                        <label for="form-username" class="control-label">Username:</label>
                        <input type="text" class="form-control" id="form-username" name="username" value="{{ old('username') }}">
                    </div>

                    <div class="form-group">
                        <label for="input-alias" class="control-label">Alias:</label>
                        <input type="text" class="form-control" id="input-alias" name="alias" value="{{ old('alias') }}">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-ok">Add</button>
            </div>
            {!! \Form::close() !!}
        </div>
    </div>
</div>

<script>
</script>