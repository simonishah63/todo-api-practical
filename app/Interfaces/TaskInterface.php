<?php

namespace App\Interfaces;

interface TaskInterface
{
    /**
     * Create New Item
     *
     * @return object Created Task
     */
    public function create(array $data);

    /**
     * Get Task Data
     *
     * @return array Search Data
     */
    public function search(array $search);
}
