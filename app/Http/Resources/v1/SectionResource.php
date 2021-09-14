<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class SectionResource extends JsonResource
{
    public function toArray( $request )
    {

        if ( $this->resource instanceof Collection ) {
            return SectionResource::collection($this->resource);
        }

        $result = [
            'id'         => $this->id,
            'name'       => $this->name,
            'image'      => $this->image(),
        ];
        return $result;
    }

    private function image()
    {
        if (!blank($this->getMedia('sections'))) {
            return asset($this->getFirstMediaUrl('sections'));
        }
        return asset( 'assets/img/default/section.png');
    }
}
