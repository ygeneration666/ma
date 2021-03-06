<?php
$parameters = array(
	'cache_path' => '%kernel.root_dir%/cache',
	'log_path' => '%kernel.root_dir%/logs',
	'site_url' => 'http://ma.labots.co',
	'install_source' => 'Mautic',
	'db_driver' => 'pdo_mysql',
	'db_host' => 'localhost',
	'db_table_prefix' => 'ma',
	'db_port' => '3306',
	'db_name' => 'autoMarket',
	'db_user' => 'root',
	'db_password' => 'labots.co',
	'db_path' => null,
	'mailer_from_name' => 'labots co',
	'mailer_from_email' => 'labots.co@gmail.com',
	'mailer_transport' => 'mail',
	'mailer_host' => null,
	'mailer_port' => null,
	'mailer_user' => null,
	'mailer_password' => null,
	'mailer_encryption' => null,
	'mailer_auth_mode' => null,
	'mailer_spool_type' => 'memory',
	'mailer_spool_path' => '%kernel.root_dir%/spool',
	'secret_key' => '4c8d4301b58c8edcdd609e87134e4f8a63be002c3a3014a22f664d3b4e706f5a',
	'webroot' => null,
	'image_path' => 'media/images',
	'theme' => 'Mauve',
	'locale' => 'ja',
	'trusted_hosts' => array(

	),
	'trusted_proxies' => array(

	),
	'rememberme_key' => 'ab90c625f80f8126309fec4fae1da7fff78001dd',
	'rememberme_lifetime' => '31536000',
	'rememberme_path' => '/',
	'rememberme_domain' => null,
	'default_pagelimit' => 30,
	'default_timezone' => 'Asia/Tokyo',
	'date_format_full' => 'F j, Y g:i a T',
	'date_format_short' => 'D, M d',
	'date_format_dateonly' => 'F j, Y',
	'date_format_timeonly' => 'g:i a',
	'ip_lookup_service' => 'telize',
	'ip_lookup_auth' => null,
	'update_stability' => 'stable',
	'cookie_path' => '/',
	'cookie_domain' => null,
	'cookie_secure' => false,
	'cookie_httponly' => false,
	'do_not_track_ips' => array(

	),
	'cat_in_page_url' => false,
	'google_analytics' => null,
	'redirect_list_types' => array(
"mautic.page.form.redirecttype.permanent", 
		"mautic.page.form.redirecttype.temporary"
	),
	'api_enabled' => false,
	'api_oauth2_access_token_lifetime' => 60,
	'api_oauth2_refresh_token_lifetime' => 14,
	'mailer_return_path' => null,
	'mailer_spool_msg_limit' => null,
	'mailer_spool_time_limit' => null,
	'mailer_spool_recover_timeout' => '900',
	'mailer_spool_clear_timeout' => '1800',
	'unsubscribe_text' => '<a href=\'|URL|\'>Unsubscribe</a> to no longer receive emails from us.',
	'webview_text' => '<a href=\'|URL|\'>Having trouble reading this email? Click here.</a>',
	'unsubscribe_message' => 'We are sorry to see you go! |EMAIL| will no longer receive emails from us. If this was by mistake, <a href=\'|URL|\'>click here to re-subscribe</a>.',
	'resubscribe_message' => '|EMAIL| has been re-subscribed. If this was by mistake, <a href=\'|URL|\'>click here to unsubscribe</a>.',
	'monitored_email' => array(
		"general" => array(
			"address" => "", 
			"host" => "", 
			"port" => "993", 
			"encryption" => "/ssl", 
			"user" => "", 
			"password" => ""
		), 
		"EmailBundle_bounces" => array(
			"address" => "", 
			"host" => "", 
			"port" => "993", 
			"encryption" => "/ssl", 
			"user" => "", 
			"password" => "", 
			"override_settings" => "", 
			"folder" => "", 
			"ssl" => "1"
		), 
		"EmailBundle_unsubscribes" => array(
			"address" => "", 
			"host" => "", 
			"port" => "993", 
			"encryption" => "/ssl", 
			"user" => "", 
			"password" => "", 
			"override_settings" => "", 
			"folder" => "", 
			"ssl" => "1"
		)
	),
	'webhook_start' => 0,
	'webhook_limit' => 1000,
	'webhook_log_max' => 10,
	'queue_mode' => 'immediate_process',
	'upload_dir' => '/var/git/repo/ma/autoMarket/app/../media/files',
	'max_size' => '6',
	'allowed_extensions' => array(
"csv", 
		"doc", 
		"docx", 
		"epub", 
		"gif", 
		"jpg", 
		"jpeg", 
		"mpg", 
		"mpeg", 
		"mp3", 
		"odt", 
		"odp", 
		"ods", 
		"pdf", 
		"png", 
		"ppt", 
		"pptx", 
		"tif", 
		"tiff", 
		"txt", 
		"xls", 
		"xlsx", 
		"wav"
	),
);
