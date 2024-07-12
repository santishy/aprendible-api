<?php

namespace App\JsonApi;

use Illuminate\Database\Eloquent\Collection;

class Document extends Collection
{
    public static function type(string $type): Document
    {

        return new self([
            "data" => [
                "type" => $type
            ]
        ]);
    }
    /**Items viene de la clase Collection es una propiedad items[] */
    public function id($id): Document
    {
        $this->items["data"]["id"] = (string) $id;
        return $this;
    }

    public function attributes($attributes): Document
    {
        $this->items["data"]["attributes"] = $attributes;
        return $this;
    }
    public function links($links): Document
    {
        $this->items["data"]["links"] = $links;
        return $this;
    }
}
