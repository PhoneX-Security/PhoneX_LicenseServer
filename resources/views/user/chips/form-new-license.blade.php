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
            <label for="password" class="control-label">Product*</label>
            <select name="product_id" class="form-control">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->uc_name }} </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-4 ">
        <div class="form-group">
            <label for="password" class="control-label">License notes</label>
            <textarea name="comment" class="form-control" rows="3">{{ old('comment') }}</textarea>
        </div>
    </div>
</div>