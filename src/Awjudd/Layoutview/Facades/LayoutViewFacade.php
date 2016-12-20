<?php namespace Awjudd\Layoutview\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\View\Environment
 */
class LayoutViewFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}
