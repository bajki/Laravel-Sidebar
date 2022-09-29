<?php

namespace Maatwebsite\Sidebar\Presentation;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Maatwebsite\Sidebar\Item;

class ActiveStateChecker
{
    /**
     * @return bool
     */
    public function isActive(Item $item)
    {
        // Check if one of the children is active
        foreach ($item->getItems() as $child) {
            if ($this->isActive($child)) {
                return true;
            }
        }
        // Custom set active path
        if ($path = $item->getActiveWhen()) {
            return Request::route()->getName() === $path;
        }
        $path = explode('.', $item->getRoute());
        $last = end($path);
        $last = str_replace(".{$last}", '', $item->getRoute());

        return Str::contains(Request::route()->getName(), $last);
    }
}
