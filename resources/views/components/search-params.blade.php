<form action="{{ route('search') }}" class="search" method="GET">
    <div class="input-group w-100">
        <!-- <select name="location" id="location" class="form-control search-location">
            <option value="">{{ __('Select location') }}</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" @if(session('location') == $location->id) selected @endif>{{ $location->name }}</option>
            @endforeach
        </select>
        <select name="area" id="area" class="form-control search-area">
            <option value="">{{ __('Select area') }}</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @if(session('area') == $area->id) selected @endif>{{ $area->name }}</option>
            @endforeach
        </select> -->
        <input type="text" class="form-control" placeholder="Search" name="name" value="{{ session('query') }}">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</form>
