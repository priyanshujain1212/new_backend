<aside class="col-md-3">
    <form action="{{ route('search') }}" class="search pb-3" method="GET">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="location">{{ __('Location') }}</label>
                    <select name="location" id="location" class="form-control search-location">
                        <option value="">{{ __('Select location') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @if(session('location') == $location->id) selected @endif>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="area">{{ __('Area') }}</label>
                    <select name="area" id="area" class="form-control search-area">
                        <option value="">{{ __('Select area') }}</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @if(session('area') == $area->id) selected @endif>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="location">{{ __('Shop & Product') }}</label>
                    <input type="text" class="form-control" placeholder="Search" name="name" value="{{ session('query') }}">
                </div>
                <button class="btn btn-block btn-primary" type="submit">
                    {{ __('Submit') }}
                </button>
            </div><!-- card-body.// -->
        </div>
    </form>
</aside>
