<?php

namespace App\Models\Traits;

trait Searchable {

    public function getSearchType() {
        return '_doc';
    }

    public function getSearchIndex() {
        return $this->gettable();
    }

    public function getId() {
        return $this->id;
    }

    public function getIndexId(){
        return $this->index_id;
    }

    abstract function toSearchArray();

}
