<?php

use Riario\Logic\Commands;

return [

    /*
    |--------------------------------------------------------------------------
    | Logic Namespace
    |--------------------------------------------------------------------------
    |
    | Default logic namespace.
    |
    */

    'namespace' => 'Logic',

    
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Logic path
        |--------------------------------------------------------------------------
        |
        | This path is used to save the generated module.
        | This path will also be added automatically to the list of scanned folders.
        |
        */

        'logic' => base_path('Logic'),
        /*
        |--------------------------------------------------------------------------
        | Logic assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the Logic' assets path.
        |
        */

        'assets' => public_path('logic'),
        
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate's key to false to not generate that folder
        */
        'generator' => [
            'action' => ['path' => 'Action', 'generate' => true],
            'config' => ['path' => 'Config', 'generate' => true],
            'policy' => ['path' => 'Policies', 'generate' => true],
            'request' => ['path' => 'Requests', 'generate' => true],
            'provider' => ['path' => 'Providers', 'generate' => true],
            'contract' => ['path' => 'Contracts', 'generate' => true],
            'service' => ['path' => 'Services', 'generate' => true],
            'route' => ['path' => 'Routes', 'generate' => true],
            'test' => ['path' => 'Tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => true],
        ],
    ],

    'stubs' => [
        'enabled' => false,
        'path' => base_path('vendor/riario/logic/src/stubs'),
        'files' => [
            'routes/api' => 'Routes/api.php',
            'config' => 'Config/config.php',
        ],
        'replacements' => [
            'routes/api' => ['LOWER_NAME', 'STUDLY_NAME'],
            'config' => ['STUDLY_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
        ],
    ],

    'commands' => [
        Commands\MakeLogicCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderCommand::class
    ],

];
