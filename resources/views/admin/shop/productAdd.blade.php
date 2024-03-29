@extends('admin.layouts.master')

@section('main-content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Shop Products') }}</h1>
            {{ Breadcrumbs::render('shop-product-add', $shop) }}
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="product_type">{{ __('Product Type') }}</label> <span class="text-danger">*</span>
                                    <select id="product_type" name="product_type" class="form-control @error('product_type') is-invalid @enderror">
                                        <option {{ old('product_type')=="0"? 'selected':''}} value="">{{ __('Select Product Type') }}</option>
                                        <option {{ old('product_type')=="5"? 'selected':''}} value="5">{{ __('Single') }}</option>
                                        <option {{ old('product_type')=="10"? 'selected':''}} value="10">{{ __('Variation') }}</option>
                                    </select>
                                    @error('product_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="product_id">{{ __('Products') }}</label> <span class="text-danger">*</span>
                                    <select id="product_id" name="product_id" class="select2 form-control @error('product_id') is-invalid @enderror">
                                        <option data-productmrp="0" value="">{{ __('Select Product') }}</option>
                                        @if(!blank($products))
                                            @foreach($products as $product)
                                                <option data-productmrp="{{ $product->unit_price ?? 0 }}" value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="single-product">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="unit_price">{{ __('Price') }}</label> <span class="text-danger">*</span>
                                    <input type="number" id="unit_price" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" step="any">
                                    @error('unit_price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="quantity">{{ __('Quantity') }}</label> <span class="text-danger">*</span>
                                    <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}">
                                    @error('quantity')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="discount_price">{{ __('Discount Price') }}</label>
                                    <input type="number" id="discount_price" name="discount_price" class="form-control @error('discount_price') is-invalid @enderror" value="{{ old('discount_price') }}" step="any">
                                    @error('discount_price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-variation">
                            <div class="col-sm-12">
                                <h2>{{ __('Product Variations') }} </h2>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Discount') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!blank(session('variation')))
                                                @foreach(session('variation') as $variation)
                                                    <?php
                                                        if($variation == 1) {
                                                            continue;
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="variation[<?=$variation?>][name]" placeholder="Name" class="form-control form-control-sm @error("variation.$variation.name") is-invalid @enderror" value="{{ old("variation.$variation.name") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("variation.$variation.price") is-invalid @enderror" value="{{ old("variation.$variation.price") }}" step="any">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity @error("variation.$variation.quantity") is-invalid @enderror" value="{{ old("variation.$variation.quantity") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="variation[<?=$variation?>][discount_price]" placeholder="Discount Price" class="form-control form-control-sm change-productdiscountprice @error("variation.$variation.discount_price") is-invalid @enderror" value="{{ old("variation.$variation.discount_price") }}" step="any">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm removeBtn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td>
                                                    <input type="text" name="variation[1][name]" placeholder="Name" class="form-control form-control-sm @error("variation.1.name") is-invalid @enderror" value="{{ old('variation.1.name') }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="variation[1][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("variation.1.price") is-invalid @enderror" value="{{ old('variation.1.price') }}" step="any">
                                                </td>
                                                <td>
                                                    <input type="number" name="variation[1][quantity]" placeholder="Quantity" class="form-control form-control-sm change-productquantity @error("variation.1.quantity") is-invalid @enderror" value="{{ old('variation.1.quantity') }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="variation[1][discount_price]" placeholder="Discount Price" class="form-control form-control-sm change-productdiscountprice @error("variation.1.discount_price") is-invalid @enderror" value="{{ old('variation.1.discount_price') }}" step="any">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" id="variation-add">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-option">
                            <div class="col-sm-12">
                                <h2>{{ __('Product Options') }} </h2>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!blank(session('option')))
                                                @foreach(session('option') as $option)
                                                    <?php
                                                        if($option == 1) {
                                                            continue;
                                                        }
                                                    ?>
                                                     <tr>
                                                        <td>
                                                            <input type="text" name="option[<?=$option?>][name]" placeholder="Name" class="form-control form-control-sm @error("option.$option.name") is-invalid @enderror" value="{{ old("option.$option.name") }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="option[<?=$option?>][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("option.$option.price") is-invalid @enderror" value="{{ old("option.$option.price") }}" step="any">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm removeBtn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td>
                                                    <input type="text" name="option[1][name]" placeholder="Name" class="form-control form-control-sm @error("option.1.name") is-invalid @enderror" value="{{ old('option.1.name') }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="option[1][price]" placeholder="Price" class="form-control form-control-sm change-productprice @error("option.1.price") is-invalid @enderror" value="{{ old('option.1.price') }}">
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" id="option-add">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button class="btn btn-primary" id="saveShopProduct" type="submit">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        var product_type = '<?=old('product_type')?>';
        var product_variation_count = <?=!blank(session('variation')) ? count(session('variation')) : 1?>;
        var product_option_count = <?=!blank(session('option')) ? count(session('option')) : 1?>;
    </script>
    <script src="{{ asset('js/shop/productAdd.js') }}"></script>
@endsection
