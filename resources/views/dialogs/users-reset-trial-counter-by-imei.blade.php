<div class="modal fade" id="modal-users-reset-trial-counter-by-imei" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                Reset trial counter by *hashed* IMEI
            </div>
            <form method="post" action="/reports/reset-trial-counter">
                <div class="modal-body">

                        <div class="form-group">
                            <label for="form-imei" class="control-label">Imei:</label>
                            <input type="text" class="form-control" id="form-imei" name="imei" value="{{ old('imei') }}">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-ok">Ok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
</script>