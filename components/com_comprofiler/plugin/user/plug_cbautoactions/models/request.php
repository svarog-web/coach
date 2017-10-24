<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionRequest extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $trigger, $user )
	{
		$return								=	null;

		foreach ( $trigger->getParams()->subTree( 'request' ) as $row ) {
			/** @var ParamsInterface $row */
			$url							=	$trigger->getSubstituteString( $row->get( 'url', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'url', null, GetterInterface::STRING ) ) ? false : array( 'cbautoactionsClass', 'escapeURL' ) ) );

			if ( ! $url ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_REQUEST_NO_URL', ':: Action [action] :: Request skipped due to missing url', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$client							=	new GuzzleHttp\Client();

			try {
				$options					=	array();

				if ( $row->get( 'auth', 'none', GetterInterface::STRING ) == 'basic' ) {
					$username				=	$trigger->getSubstituteString( $row->get( 'auth_username', null, GetterInterface::STRING ) );
					$password				=	$trigger->getSubstituteString( $row->get( 'auth_password', null, GetterInterface::STRING ) );

					if ( $username && $password ) {
						$options['auth']	=	array( $username, $password );
					}
				}

				$body						=	array();

				foreach ( $row->subTree( 'request' ) as $request ) {
					/** @var ParamsInterface $request */
					$key					=	$request->get( 'key', null, GetterInterface::STRING );

					if ( $key ) {
						$body[$key]			=	$trigger->getSubstituteString( $request->get( 'value', null, GetterInterface::RAW ), false, $request->get( 'translate', false, GetterInterface::BOOLEAN ) );
					}
				}

				$headers					=	array();

				foreach ( $row->subTree( 'header' ) as $header ) {
					/** @var ParamsInterface $header */
					$key					=	$header->get( 'key', null, GetterInterface::STRING );

					if ( $key ) {
						$headers[$key]		=	$trigger->getSubstituteString( $header->get( 'value', null, GetterInterface::RAW ), false, $header->get( 'translate', false, GetterInterface::BOOLEAN ) );
					}
				}

				if ( $headers ) {
					$options['headers']		=	$headers;
				}

				if ( $row->get( 'method', 'GET', GetterInterface::STRING ) == 'POST' ) {
					if ( $body ) {
						$options['body']	=	$body;
					}

					$result					=	$client->post( $url, $options );
				} else {
					if ( $body ) {
						$options['query']	=	$body;
					}

					$result					=	$client->get( $url, $options );
				}

				if ( $result->getStatusCode() != 200 ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_REQUEST_FAILED', ':: Action [action] :: Request failed. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $result->getStatusCode() ) ) );
						var_dump( $result );
					}

					$result					=	false;
				}
			} catch ( Exception $e ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_REQUEST_FAILED', ':: Action [action] :: Request failed. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $e->getMessage() ) ) );
				}

				$result						=	false;
			}

			if ( $result !== false ) {
				switch( $result->getHeader( 'Content-Type' ) ) {
					case 'application/xml':
						$content			=	CBTxt::T( 'HTTP Request XML response handling is not yet implemented.' );
						break;
					case 'application/json':
						$content			=	$this->jsonResults( $result->json() );
						break;
					default:
						$content			=	$result->getBody();
						break;
				}

				switch ( $row->get( 'return', 'ECHO', GetterInterface::STRING ) ) {
					case 'ECHO':
						echo $content;
						break;
					case 'DUMP':
						var_dump( $content );
						break;
					case 'PRINT':
						print $content;
						break;
					case 'RETURN':
						$return				.=	$content;
						break;
				}
			}
		}

		return $return;
	}

	/**
	 * @param array|object $json
	 * @return null|string
	 */
	private function jsonResults( $json )
	{
		$return				=	null;

		foreach ( $json as $k => $v ) {
			$return			.=	'<div class="form-group cb_form_line clearfix">';

			if ( trim( $k ) !== '' ) {
				$return		.=		'<label class="control-label col-sm-3">'
							.			$k
							.		'</label>';

				$size		=	'col-sm-9';
			} else {
				$size		=	'col-sm-9 col-sm-offset-3';
			}

			$return			.=		'<div class="cb_field ' . $size . '">'
							.			'<div>';

			if ( is_object( $v ) || is_array( $v ) ) {
				$return		.=				$this->jsonResults( $v );
			} else {
				$return		.=				$v;
			}

			$return			.=			'</div>'
							.		'</div>'
							.	'</div>';
		}

		return $return;
	}
}