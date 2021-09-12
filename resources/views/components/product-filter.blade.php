<aside class="col-md-3">
    <form action="{{ route('search-product', [$shop]) }}" class="search pb-3" method="GET">
        <div class="card">
            <article class="filter-group">
                <div class="filter-content collapse show">
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Product Search" name="name" value="{{ session('product-query') }}">
                        </div>
                    </div> <!-- card-body.// -->
                </div>
            </article> <!-- filter-group  .// -->
            <article class="filter-group">
                <header class="card-header">
                    <h6 class="title">{{ __('Categories') }} </h6>
                </header>
                <div class="filter-content collapse show">
                    <div class="card-body">
                        @foreach($categories as $category)
                            <label class="custom-control custom-checkbox">
                                <input @if(session()->has('shop-categories') && in_array($category->id, session()->get('shop-categories'))) checked @endif type="checkbox" class="custom-control-input"
                                       name="categories[]" value="{{ $category->id }}"
                                       >
                                <span class="custom-control-label">
                                    {{ $category->name }}
                                </span>
                            </label>
                        @endforeach
                        <button class="btn btn-block btn-primary" type="submit">{{ __('Apply') }}</button>
                    </div>
                </div>
            </article>
        </div>
    </form>
</aside>
