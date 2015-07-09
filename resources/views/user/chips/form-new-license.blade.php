<div class="form-group">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="starts_at" class="control-label">Start date*</label>
                <div class="input-group date">
                    <input value="{{ old('starts_at') }}" type="text" name="starts_at" class="form-control" />
                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(function () {
                $('#starts_at').daterangepicker();
            });
        </script>

        <div class="col-md-2">
            <label for="password" class="control-label">Expiration*</label>
            <select name="license_type_id" class="form-control">
                @foreach($licenseTypes as $type)
                    <option value="{{ $type->id }}">{{ ucfirst($type->name) . " (" . $type->days . " days)" }} </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="password" class="control-label">Type*</label>
            <select name="license_func_type_id" class="form-control">
                @foreach($licenseFuncTypes as $type)
                    <option value="{{ $type->id }}">{{ ucfirst($type->name) }} </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-4 ">
        <div class="form-group">
            <label for="password" class="control-label">License notes</label>
            <textarea name="comment" class="form-control" rows="3"></textarea>
        </div>
    </div>
</div>