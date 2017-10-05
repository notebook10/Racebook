<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Inherit from another theme
	|--------------------------------------------------------------------------
	|
	| Set up inherit from another if the file is not exists, this 
	| is work with "layouts", "partials", "views" and "widgets"
	|
	| [Notice] assets cannot inherit.
	|
	*/

	'inherit' => null, //default

	/*
	|--------------------------------------------------------------------------
	| Listener from events
	|--------------------------------------------------------------------------
	|
	| You can hook a theme when event fired on activities this is cool 
	| feature to set up a title, meta, default styles and scripts.
	|
	| [Notice] these event can be override by package config.
	|
	*/

	'events' => array(

		'before' => function($theme)
		{
			$theme->setTitle('Title example');
			$theme->setAuthor('Kiko');
		},

		'asset' => function($asset)
		{
            $asset->add([
//                ['bootstrap-css', 'css/bootstrap/bootstrap.min.css'],
//                ['jquery', 'js/jquery-3.2.1.min.js'],
//                ['bootstrap-js', 'js/bootstrap/bootstrap.min.js'],
//                ['js-sweetalert', 'js/sweetalert.min.js']
            ]);

			$asset->themePath()->add([
//                ['style', 'css/style.css'],
//                ['script', 'js/script.js'],
             ]);

			// You may use elixir to concat styles and scripts.




			// Or you may use this event to set up your assets.
			/*
			$asset->themePath()->add('core', 'core.js');
			$asset->add([
							['jquery', 'vendor/jquery/jquery.min.js'],
							['jquery-ui', 'vendor/jqueryui/jquery-ui.min.js', ['jquery']]
						]);
			*/
		},


		'beforeRenderTheme' => function($theme)
		{
//            $theme->asset()->add('css-bootstrap-min', 'css/bootstrap/bootstrap.min.css');
//            $theme->asset()->themePath()->add('css-style', 'css/style.css');
			// To render partial composer
			/*
	        $theme->partialComposer('header', function($view){
	            $view->with('auth', Auth::user());
	        });
			*/
            $theme->asset()->add('css-bootstrap-min', 'css/bootstrap/bootstrap.min.css');
            $theme->asset()->add('css-sweetalert', 'css/sweetalert.css');
            $theme->asset()->add('css-datatable', 'css/datatables.min.css');
//            $theme->asset()->add('css-mCustomScrollbar', 'css/jquery.mCustomScrollbar.min.css');
            $theme->asset()->add('css-jquery-ui', 'css/jquery-ui.min.css');

            $theme->asset()->add('js-jquery-3.2.1', 'js/jquery-3.2.1.min.js');
            $theme->asset()->add('js-bootstrap-min', 'js/bootstrap/bootstrap.min.js');
            $theme->asset()->add('js-sweetalert', 'js/sweetalert.min.js');
            $theme->asset()->add('js-datatable', 'js/jquery.datatables.js');
//            $theme->asset()->add('js-mCustomScrollbar', 'js/jquery.mCustomScrollbar.concat.min.js');
            $theme->asset()->add('js-jquery-ui', 'js/jquery-ui.min.js');
		},

		'beforeRenderLayout' => array(

			'mobile' => function($theme)
			{
				// $theme->asset()->themePath()->add('ipad', 'css/layouts/ipad.css');
			}

		)

	)

);