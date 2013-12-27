<?php namespace Awjudd\Layoutview;

use Illuminate\View\Environment;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Events\Dispatcher;

use Config;

class LayoutView extends Environment
{
    /**
     * Contains all of the namespaces in order that the view should look at
     * before actually rendering.
     * 
     * @var array
     */
    protected $namespaces = array();

    /**
     * The current layout folder that is selected
     * 
     * @var string
     */
    public $selected_layout = NULL;

    /**
     * The layout folder that is used if a view isn't available in the current selected
     * layout.
     * 
     * @var string
     */
    public $fallback_layout = NULL;

    /**
     * Create a new view environment instance.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $engines
     * @param  \Illuminate\View\ViewFinderInterface  $finder
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events)
    {
        // Call the parent constructor
        parent::__construct($engines, $finder, $events);
    }

    public function setSelectedLayout($layout)
    {
        $this->selected_layout = $layout;
    }

    public function setFallbackLayout($layout)
    {
        $this->fallback_layout = $layout;
    }

    /**
     * Get a evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        // Grab the namespace information from the configuration file
        $this->namespaces = Config::get('layoutview::namespaces');

        $target = NULL;

        // Cycle through all of the namespaces
        foreach($this->namespaces as $namespace)
        {
            // Derive the base view
            $derived = $namespace . $this->selected_layout . '.' . $view;

            // Look to see if the view exists
            if(self::exists($derived))
            {
                // It does, so render it
                $target = $derived;
            }
            else
            {
                // It didn't so try in the fallback
                $derived = $namespace . $this->fallback_layout . '.' . $view;

                // Check if the fallback view exists
                if(self::exists($derived))
                {
                    $target = $derived;
                }
            }

            // Check if we found a match
            if($target !== NULL)
            {
                // We did, so no sense looping
                break;
            }
        }

        return parent::make($target, $data, $mergeData);;
    }
}