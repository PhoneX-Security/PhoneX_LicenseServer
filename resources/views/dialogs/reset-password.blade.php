<div class="modal fade" id="reset-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Reset SIP password
            </div>
            <div class="modal-body">
                {!! \Form::open(['method' => 'patch', 'route' => ['users.change_sip_pass', $user->id]]) !!}
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Password:</label>
                        <input type="text" class="form-control" id="recipient-name" value="{{ old('sip_default_password','phonexxx') }}">
                    </div>
                {!! \Form::close() !!}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Reset</a>
            </div>
        </div>
    </div>
</div>


<script>
    // TODO fire form
    $('#reset-password').on('show.bs.modal', function(e) {
//        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

//        $('.debug-url').html('Delete URL: <strong>' + $(this).find('.btn-ok').attr('href') + '</strong>');
    });
</script>