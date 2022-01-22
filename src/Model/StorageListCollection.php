<?php namespace NurgisaA\CommerceML\Model;

use NurgisaA\CommerceML\ORM\Collection;

class StorageListCollection extends Collection
{
    /**
     * Get price type by id.
     *
     * @param $name
     * @return string
     */
    public function getType($name)
    {
        return $this->get($name)->name;
    }
}
