<?php
/**
 * WebChat
 *
 * Integrates a web chat client in to a special page, for example Mibbit.
 *
 * @file
 * @ingroup Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:WebChat
 *
 * @author Robert Leverington <robert@rhl.me.uk>
 * @copyright Copyright © 2008 - 2009 Robert Leverington.
 * @copyright Copyright © 2009 Marco 27.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// If this is run directly from the web die as this is not a valid entry point.
if ( !defined( 'MEDIAWIKI' ) ) { die( 'Invalid entry point.' );
}

// Extension credits.
$wgExtensionCredits[ 'specialpage' ][] = [
	'path'           => __FILE__,
	'name'           => 'WebChat',
	'descriptionmsg' => 'webchat-desc',
	'author'         => [ 'Robert Leverington', 'Marco 27' ],
	'url'            => 'https://www.mediawiki.org/wiki/Extension:WebChat',
	'version'        => '1.3.0',
];

$dir = __DIR__ . '/';

// Register special page.
$wgSpecialPages['WebChat'] = 'WebChat';
$wgAutoloadClasses['WebChat'] = $dir . 'WebChat_body.php';

// Extension messages.
$wgMessagesDirs['WebChat'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['WebChatAlias'] = $dir . 'WebChat.alias.php';

// Default configuration.
$wgWebChatServer  = '';
$wgWebChatChannel = '';
$wgWebChatClient  = '';
$wgWebChatClients = [
	'Mibbit' => [
		'url' => 'http://embed.mibbit.com/index.html',
		'parameters' => [
			'noServerMotd' => 'true',
			'server'  => '$$$server$$$',
			'channel' => '$$$channel$$$',
			'nick'    => '$$$nick$$$',
		],
	],
	'freenodeChat' => [
		'url' => '//webchat.freenode.net/',
		'parameters' => [
			'channels' => '$$$channel$$$',
			'nick'    => '$$$nick$$$',
		],
	]
];

// Default permissions.
$wgAvailableRights[] = 'webchat';
$wgGroupPermissions['*'    ]['webchat'] = false;
$wgGroupPermissions['user' ]['webchat'] = true;
$wgGroupPermissions['sysop']['webchat'] = true;
