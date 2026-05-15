<?php
/**
 * WebChat extension special page class.
 */

use MediaWiki\Config\Config;
use MediaWiki\Config\ConfigException;
use MediaWiki\Html\Html;

class WebChat extends SpecialPage {
	public function __construct(
		private readonly Config $config,
	) {
		parent::__construct( 'WebChat' );
	}

	/** @inheritDoc */
	public function getRestriction(): string {
		return 'webchat';
	}

	public function execute( $par ) {
		$this->setHeaders();
		$this->getOutput()->addWikiMsg( 'webchat-header' );

		$client = $this->config->get( 'WebChatClient' );
		$clients = $this->config->get( 'WebChatClients' );

		if ( !array_key_exists( $client, $clients ) ) {
			throw new ConfigException( 'Unknown web chat client specified.' );
		}

		$config = $clients[$client];
		$parameters = $config['parameters'];
		foreach ( $parameters as &$value ) {
			$value = $this->replaceVariables( $value );
		}
		$hash = $this->replaceVariables( $config['hash'] ?? '' );
		$url = wfAppendQuery( $config['url'], $parameters ) . $hash;

		$this->getOutput()->addHTML( Html::openElement( 'iframe', [
			'width'     => '100%',
			'height'    => '500',
			'scrolling' => 'no',
			'border'    => '0',
			'onLoad'    => 'webChatExpand( this )',
			'src'       => $url
		] ) . Html::closeElement( 'iframe' ) );

		// Hack to make the chat area a reasonable size.
		$this->getOutput()->addHTML( Html::rawElement( 'script',
			[ 'type' => 'text/javascript' ],
'/* <![CDATA[ */
function webChatExpand( elem ) {
	height = elem.height;
	elem.height = screen.height - 500;
}
/* ]]> */'
			) );
	}

	protected function getGroupName() {
		return 'wiki';
	}

	private function replaceVariables( string $value ): string {
		switch ( $value ) {
			case '$$$nick$$$':
				if ( $this->getUser()->isRegistered() ) {
					$value = str_replace( ' ', '_', $this->getUser()->getName() );
				}
				break;
			case '$$$channel$$$':
				$value = $this->config->get( 'WebChatChannel' );
				break;
			case '$$$server$$$':
				$value = $this->config->get( 'WebChatServer' );
				break;
		}
		return $value;
	}
}
