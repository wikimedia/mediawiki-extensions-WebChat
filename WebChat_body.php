<?php
/**
 * WebChat extension special page class.
 */

class WebChat extends SpecialPage {
	public function __construct() {
		parent::__construct( 'WebChat', 'webchat' );
	}

	public function execute( $par ) {
		global $wgWebChatClient, $wgWebChatClients;

		$this->setHeaders();
		$this->getOutput()->addWikiMsg( 'webchat-header' );

		if ( !array_key_exists( $wgWebChatClient, $wgWebChatClients ) ) {
			throw new MWException( 'Unknown web chat client specified.' );
		}

		$config = $wgWebChatClients[$wgWebChatClient];
		$parameters = $config['parameters'];
		foreach ( $parameters as &$value ) {
			$value = $this->replaceVariables( $value );
		}
		$hash = $this->replaceVariables( $config['hash'] ?? '' );
		$url = wfAppendQuery( $config['url'], $parameters ) . $hash;

		$this->getOutput()->addHTML( Xml::openElement( 'iframe', [
			'width'     => '100%',
			'height'    => '500',
			'scrolling' => 'no',
			'border'    => '0',
			'onLoad'    => 'webChatExpand( this )',
			'src'       => $url
		] ) . Xml::closeElement( 'iframe' ) );

		// Hack to make the chat area a reasonable size.
		$this->getOutput()->addHTML( Xml::tags( 'script',
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
		global $wgWebChatServer, $wgWebChatChannel;

		switch ( $value ) {
			case '$$$nick$$$':
				if ( $this->getUser()->isRegistered() ) {
					$value = str_replace( ' ', '_', $this->getUser()->getName() );
				}
				break;
			case '$$$channel$$$':
				$value = $wgWebChatChannel;
				break;
			case '$$$server$$$':
				$value = $wgWebChatServer;
				break;
		}
		return $value;
	}
}
