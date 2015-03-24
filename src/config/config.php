<?php
/* 
* @Author: Andrew Judd
* @Date:   2014-09-21 21:52:34
* @Last Modified by:   Andrew Judd
* @Last Modified time: 2014-09-21 21:58:56
*/

return [

    /**
     * Contains all of the layout-specific configuration values
     * 
     * @var array
     */
    'layout' => [

        /**
         * The layout that will be used to fall back on if the selected doesn't exist.
         * 
         * @var string
         */
        'fallback' => null,

        /**
         * The layout that will be the first choice to be used.
         * 
         * @var string
         */
        'selected' => null,
    ],

    /**
     * Contains all of the namespaces in order that the view should look at
     * before actually rendering.
     * 
     * @var array
     */
    'namespaces' => [
        /*
         * Should always keep a blank one here to search in no namespaces.
         */
        '',
    ],
];