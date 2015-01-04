<?php namespace Awjudd\Layoutview;

use Illuminate\View\Factory;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Events\Dispatcher;

use Config;

class LayoutView extends Factory
{
    /**
     * Contains all of the namespaces in order that the view should look at
     * before actually rendering.
     * 
     * @var array
     */
    protected $namespaces = NULL;

    /**
     * The current layout folder that is selected
     * 
     * @var string
     */
    private $selected_layout = NULL;

    /**
     * The layout folder that is used if a view isn't available in the current selected
     * layout.
     * 
     * @var string
     */
    private $fallback_layout = NULL;

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

    /**
     * The layout that is being used for the core of display purposes.
     * 
     * @param string $layout
     */
    public function setSelectedLayout($layout)
    {
        $this->selected_layout = $layout;
    }

    /**
     * The fallback layout if the view doesn't exist in the selected layout.
     * 
     * @param string $layout
     */
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
        // Render the layout
        return parent::make($this->deriveView($view), $data, $mergeData);
    }

    /**
     * Used to dervive the view that will be displayed.
     * 
     * @return string
     */
    private function deriveView($view)
    {
        // Check if the view already exists as specified
        if(self::exists($view))
        {
            // It does so just return it
            return $view;
        }

        // Check if there are any namespaces yet
        if($this->namespaces === NULL)
        {
            // There aren't so grab the namespace information from the configuration file
            $this->namespaces = Config::get('layoutview::config.namespaces');
        }

        // Check if the selected layout has been set
        if($this->selected_layout === NULL)
        {
            // It hasn't been, so set it based on the config values
            $this->selected_layout = Config::get('layoutview::config.layout.selected');
        }

        // Check if the fallback layout has been set
        if($this->fallback_layout === NULL)
        {
            // It hasn't been, so set it based on the config values
            $this->fallback_layout = Config::get('layoutview::config.layout.fallback');
        }

        // The view that will be rendered
        $target = NULL;

        // Cycle through all of the namespaces
        foreach($this->namespaces as $namespace)
        {
            // Does the namespace have the colons in it?
            if($namespace != '' && stripos($namespace, '::') === FALSE)
            {
                // Didn't exist, so append it
                $namespace .= '::';
            }

            // Derive the base view
            $derived = $namespace . (is_null($this->selected_layout) ? '' :  $this->selected_layout . '.') . $view;

            // Look to see if the view exists
            if(self::exists($derived))
            {
                // It does, so render it
                $target = $derived;
            }
            else
            {
                // It didn't so try in the fallback
                $derived = $namespace . (is_null($this->fallback_layout) ? '' : $this->fallback_layout .  '.') . $view;

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

        // Return the name of the view we found.
        return $target === NULL ? $view : $target;
    }
}
