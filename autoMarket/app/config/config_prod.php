<?php
$loader->import("config.php");

if (file_exists(__DIR__ . '/security_local.php')) {
    $loader->import("security_local.php");
} else {
    $loader->import("security.php");
}

/*
$container->loadFromExtension("framework", array(
    "validation" => array(
        "cache" => "apc"
    )
));

$container->loadFromExtension("doctrine", array(
    "orm" => array(
        "metadata_cache_driver" => "apc",
        "result_cache_driver"   => "apc",
        "query_cache_driver"    => "apc"
    )
));
*/

$debugMode = $container->getParameter('kernel.debug');
$container->loadFromExtension("monolog", array(
    "channels" => array(
        "mautic",
    ),
    "handlers" => array(
        "main"    => array(
            "type"         => "fingers_crossed",
            "buffer_size"  => "200",
            "action_level" => ($debugMode) ? "debug" : "error",
            "handler"      => "nested",
            "channels" => array(
                "!mautic"
            )
        ),
        "nested"  => array(
            "type"  => "rotating_file",
            "path"  => "%kernel.logs_dir%/%kernel.environment%.php",
            "level" => ($debugMode) ? "debug" : "error",
            "max_files" => 7
        ),
        "mautic"    => array(
            "type"  => "rotating_file",
            "path"  => "%kernel.logs_dir%/mautic_%kernel.environment%.php",
            "level" => ($debugMode) ? "debug" : "notice",
            'channels' => array(
                'mautic',
            ),
            "max_files" => 7
        )
    )
));

// Allow overriding config without a requiring a full bundle or hacks
if (file_exists(__DIR__ . '/config_override.php')) {
    $loader->import("config_override.php");
}

// Allow local settings without committing to git such as swift mailer delivery address overrides
if (file_exists(__DIR__ . '/config_local.php')) {
    $loader->import("config_local.php");
}