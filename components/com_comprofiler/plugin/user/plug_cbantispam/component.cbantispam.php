<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Application\Application;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbantispam extends cbPluginHandler
{
	private $_tab	=	null;

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 * @return string                HTML
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_framework;

		$format							=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action							=	$this->input( 'action', null, GetterInterface::STRING );
		$function						=	$this->input( 'func', null, GetterInterface::STRING );
		$id								=	$this->input( 'id', null, GetterInterface::STRING );
		$userId							=	(int) $this->input( 'usr', null, GetterInterface::INT );

		$this->_tab						=	(int) $this->input( 'tab', null, GetterInterface::INT );

		if ( $userId ) {
			$user						=	CBuser::getUserDataInstance( (int) $userId );
		} else {
			$user						=	CBuser::getMyUserDataInstance();
		}

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'prune':
				switch ( $function ) {
					case 'block':
					case 'log':
					case 'attempts':
						$this->pruneItems( $function, false );
						break;
					case 'all':
					default:
						$this->pruneAll( false );
						break;
				}
				break;
			case 'captcha':
				switch ( $function ) {
					case 'question':
					case 'internal':
					case 'image':
						$this->captchaImage( $id, $function );
						break;
					case 'audio':
						$this->captchaAudio( $id );
						break;
				}
				break;
			case 'block':
				if ( ! $this->_tab ) {
					$this->_tab			=	'cbantispamTabBlocks';
				}

				$profileUrl				=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

				if ( ! $this->params->get( 'general_block', 1 ) ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( (int) $user->get( 'id' ) )->isGlobalModerator() ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'user':
						$this->showBlock( $id, 'user', $user );
						break;
					case 'ip':
						$this->showBlock( $id, 'ip', $user );
						break;
					case 'email':
						$this->showBlock( $id, 'email', $user );
						break;
					case 'domain':
						$this->showBlock( $id, 'domain', $user );
						break;
					case 'edit':
						$this->showBlock( $id, null, $user );
						break;
					case 'new':
						$this->showBlock( null, null, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveBlock( $id, $user );
						break;
					case 'delete':
						$this->deleteBlock( $id, $user );
						break;
					case 'show':
					default:
						cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
						break;
				}
				break;
			case 'whitelist':
				if ( ! $this->_tab ) {
					$this->_tab			=	'cbantispamTabWhitelists';
				}

				$profileUrl				=	$_CB_framework->userProfileUrl( $user->get( 'id' ), false, $this->_tab );

				if ( ! $this->params->get( 'general_whitelist', 1 ) ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( (int) $user->get( 'id' ) )->isGlobalModerator() ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'user':
						$this->showWhitelist( $id, 'user', $user );
						break;
					case 'ip':
						$this->showWhitelist( $id, 'ip', $user );
						break;
					case 'email':
						$this->showWhitelist( $id, 'email', $user );
						break;
					case 'domain':
						$this->showWhitelist( $id, 'domain', $user );
						break;
					case 'edit':
						$this->showWhitelist( $id, null, $user );
						break;
					case 'new':
						$this->showWhitelist( null, null, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveWhitelist( $id, $user );
						break;
					case 'delete':
						$this->deleteWhitelist( $id, $user );
						break;
					case 'show':
					default:
						cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
						break;
				}
				break;
			case 'attempt':
				if ( ! $this->_tab ) {
					$this->_tab			=	'cbantispamTabAttempts';
				}

				$profileUrl				=	$_CB_framework->userProfileUrl( $user->get( 'id' ), false, $this->_tab );

				if ( ! $this->params->get( 'general_attempts', 1 ) ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( (int) $user->get( 'id' ) )->isGlobalModerator() ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'delete':
						$this->deleteAttempt( $id, $user );
						break;
					case 'show':
					default:
						cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
						break;
				}
				break;
			case 'log':
				if ( ! $this->_tab ) {
					$this->_tab			=	'cbantispamTabLog';
				}

				$profileUrl				=	$_CB_framework->userProfileUrl( $user->get( 'id' ), false, $this->_tab );

				if ( ! $this->params->get( 'general_log', 1 ) ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( (int) $user->get( 'id' ) )->isGlobalModerator() ) {
					cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'delete':
						$this->deleteLog( $id, $user );
						break;
					case 'show':
					default:
						cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
						break;
				}
				break;
			default:
				cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				break;
		}

		if ( $format != 'raw' ) {
			$html						=	ob_get_contents();
			ob_end_clean();

			$class						=	$this->params->get( 'general_class', null );

			$return						=	'<div id="cbAntiSpam" class="cbAntiSpam' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
											.		'<div id="cbAntiSpamInner" class="cbAntiSpamInner">'
											.			$html
											.		'</div>'
											.	'</div>';

			echo $return;
		}
	}

	/**
	 * Prunes old items of $type
	 *
	 * @param string $type
	 * @param bool   $force
	 */
	private function pruneItems( $type, $force )
	{
		global $_CB_framework, $_CB_database;

		$clean					=	false;

		if ( $force ) {
			$clean				=	true;
		} else {
			$token				=	$this->input( 'token', null, GetterInterface::STRING );

			if ( $token == md5( $_CB_framework->getCfg( 'secret' ) ) ) {
				$clean			=	true;
			}
		}

		if ( $clean ) {
			switch ( $type ) {
				case 'block':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' );
					$_CB_database->setQuery( $query );
					$blocks		=	$_CB_database->loadObjectList( null, 'cbantispamBlockTable', array( $_CB_database ) );

					/** @var cbantispamBlockTable[] $blocks */
					foreach ( $blocks as $block ) {
						if ( $block->isExpired() ) {
							$block->delete();
						}
					}
					break;
				case 'log':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( $_CB_framework->getUTCDate( 'Y-m-d H:i:s', $this->params->get( 'cleanup_log_dur', '-1 YEAR' ) ) );
					$_CB_database->setQuery( $query );
					$logs		=	$_CB_database->loadObjectList( null, 'cbantispamLogTable', array( $_CB_database ) );

					/** @var cbantispamLogTable[] $logs */
					foreach ( $logs as $log ) {
						$log->delete();
					}
					break;
				case 'attempts':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( $_CB_framework->getUTCDate( 'Y-m-d H:i:s', $this->params->get( 'cleanup_attempts_dur', '-1 YEAR' ) ) );
					$_CB_database->setQuery( $query );
					$attempts	=	$_CB_database->loadObjectList( null, 'cbantispamAttemptsTable', array( $_CB_database ) );

					/** @var cbantispamAttemptsTable[] $attempts */
					foreach ( $attempts as $attempt ) {
						$attempt->delete();
					}
					break;
			}

			if ( ! $force ) {
				header( 'HTTP/1.0 200 OK' );
				exit();
			}
		} else {
			if ( ! $force ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit();
			}
		}
	}

	/**
	 * Prunes all old items
	 *
	 * @param bool $force
	 */
	private function pruneAll( $force )
	{
		global $_CB_framework;

		$clean			=	false;

		if ( $force ) {
			$clean		=	true;
		} else {
			$token		=	$this->input( 'token', null, GetterInterface::STRING );

			if ( $token == md5( $_CB_framework->getCfg( 'secret' ) ) ) {
				$clean	=	true;
			}
		}

		if ( $clean ) {
			$this->pruneItems( 'block', true );
			$this->pruneItems( 'log', true );
			$this->pruneItems( 'attempts', true );

			if ( ! $force ) {
				header( 'HTTP/1.0 200 OK' );
				exit();
			}
		} else {
			if ( ! $force ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit();
			}
		}
	}

	/**
	 * Generates a captcha image
	 *
	 * @param string $id
	 * @param string $function
	 */
	public function captchaImage( $id, $function )
	{
		global $_PLUGINS;

		if ( ! $id ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		while ( @ob_end_clean() );

		$absPath						=	$_PLUGINS->getPluginPath( $this->getPluginId() );
		$captcha						=	cbantispamCaptcha::getInstance( $id, ( $function != 'image' ? $function : null ) );
		$code							=	$captcha->getCaptchaCode();

		if ( $this->input( 'ver', null, GetterInterface::STRING ) ) {
			$code						=	$captcha->generateCode();
		}

		$height							=	(int) $this->params->get( 'captcha_internal_height', 40 );

		if ( ! $height ) {
			$height						=	40;
		}

		$bgColor						=	$this->params->get( 'captcha_internal_bg_color', '255,255,255' );

		if ( ! $bgColor ) {
			$bgColor					=	'255,255,255';
		}

		$bgColors						=	explode( ',', $bgColor );
		$imgBgColors					=	array();

		if ( $bgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $bgColors[$i] ) ) {
				$color					=	(int) trim( $bgColors[$i] );
				$imgBgColors[]			=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgBgColors ) {
			$imgBgColors				=	array( 255, 255, 255 );
		} elseif ( count( $imgBgColors ) < 3 ) {
			if ( count( $imgBgColors ) < 2 ) {
				$imgBgColors[]			=	255;
				$imgBgColors[]			=	255;
			} else {
				$imgBgColors[]			=	255;
			}
		}

		$txtColor						=	$this->params->get( 'captcha_internal_txt_color', '20,40,100' );

		if ( ! $txtColor ) {
			$txtColor					=	'20,40,100';
		}

		$txtColors						=	explode( ',', $txtColor );
		$imgTxtColors					=	array();

		if ( $txtColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $txtColors[$i] ) ) {
				$color					=	(int) trim( $txtColors[$i] );
				$imgTxtColors[]			=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgTxtColors ) {
			$imgTxtColors				=	array( 20, 40, 100 );
		} elseif ( count( $imgTxtColors ) < 3 ) {
			if ( count( $imgTxtColors ) < 2 ) {
				$imgTxtColors[]			=	40;
				$imgTxtColors[]			=	100;
			} else {
				$imgTxtColors[]			=	100;
			}
		}

		$nBgColor						=	$this->params->get( 'captcha_internal_bg_noise_color', '100,120,180' );

		if ( ! $nBgColor ) {
			$nBgColor					=	'100,120,180';
		}

		$nBgColors						=	explode( ',', $nBgColor );
		$imgNoiseBgColors				=	array();

		if ( $nBgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $nBgColors[$i] ) ) {
				$color					=	(int) trim( $nBgColors[$i] );
				$imgNoiseBgColors[]		=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgNoiseBgColors ) {
			$imgNoiseBgColors			=	array( 100, 120, 180 );
		} elseif ( count( $imgNoiseBgColors ) < 3 ) {
			if ( count( $imgNoiseBgColors ) < 2 ) {
				$imgNoiseBgColors[]		=	120;
				$imgNoiseBgColors[]		=	180;
			} else {
				$imgNoiseBgColors[]		=	180;
			}
		}

		$nFgColor						=	$this->params->get( 'captcha_internal_fg_noise_color', '100,120,120' );

		if ( ! $nFgColor ) {
			$nFgColor					=	'100,120,120';
		}

		$nFgColors						=	explode( ',', $nFgColor );
		$imgNoiseFgColors				=	array();

		if ( $nFgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $nFgColors[$i] ) ) {
				$color					=	(int) trim( $nFgColors[$i] );
				$imgNoiseFgColors[]		=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgNoiseFgColors ) {
			$imgNoiseFgColors			=	array( 100, 120, 120 );
		} elseif ( count( $imgNoiseFgColors ) < 3 ) {
			if ( count( $imgNoiseFgColors ) < 2 ) {
				$imgNoiseFgColors[]		=	120;
				$imgNoiseFgColors[]		=	120;
			} else {
				$imgNoiseFgColors[]		=	120;
			}
		}

		$font							=	$this->params->get( 'captcha_internal_font', 'monofont.ttf' );

		if ( ! $font ) {
			$font						=	'monofont.ttf';
		}

		if ( ! file_exists( $absPath . '/fonts/' . $font ) ) {
			$imgFont					=	$absPath . '/fonts/monofont.ttf';
		} else {
			$imgFont					=	$absPath . '/fonts/' . $font;
		}

		if ( ! file_exists( $imgFont ) ) {
			exit( CBTxt::T( 'CAPTCHA_FONT_FILE_FAILED', 'failed to locate "[file]" font file', array( '[file]' => $imgFont ) ) );
		}

		$fontSize						=	( $height * 0.75 );
		$textBox						=	imagettfbbox( $fontSize, 0, $imgFont, $code );

		if ( $textBox === false ) {
			exit( CBTxt::T( 'imagettfbbox failed to establish image size' ) );
		}

		$width							=	( $textBox[4] + 20 );
		$image							=	imagecreate( $width, $height );

		if ( $image === false ) {
			exit( CBTxt::T( 'imagecreate failed to create new image' ) );
		}

		$charRotation					=	(int) $this->params->get( 'captcha_internal_rotation', 13 );

		if ( $charRotation < 0 ) {
			$charRotation				=	0;
		}

		$charOffset						=	(int) $this->params->get( 'captcha_internal_offset', 3 );

		if ( $charOffset < 0 ) {
			$charOffset					=	0;
		}

		$colorRange						=	(int) $this->params->get( 'captcha_internal_color_range', 2 );

		if ( $colorRange < 0 ) {
			$colorRange					=	0;
		}

		if ( $colorRange ) {
			$bgColorR					=	mt_rand( ( ( $imgBgColors[0] - $colorRange ) < 0 ? 0 : ( $imgBgColors[0] - $colorRange ) ), ( ( $imgBgColors[0] + $colorRange ) > 255 ? 255 : ( $imgBgColors[0] + $colorRange ) ) );
			$bgColorG					=	mt_rand( ( ( $imgBgColors[1] - $colorRange ) < 0 ? 0 : ( $imgBgColors[1] - $colorRange ) ), ( ( $imgBgColors[1] + $colorRange ) > 255 ? 255 : ( $imgBgColors[1] + $colorRange ) ) );
			$bgColorB					=	mt_rand( ( ( $imgBgColors[2] - $colorRange ) < 0 ? 0 : ( $imgBgColors[2] - $colorRange ) ), ( ( $imgBgColors[2] + $colorRange ) > 255 ? 255 : ( $imgBgColors[2] + $colorRange ) ) );

			if ( imagecolorallocate( $image, $bgColorR, $bgColorG, $bgColorB ) === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_COLOR_FAILED', 'imagecolorallocate failed to set BG colors [r], [g], [b]', array( '[r]' => $bgColorR, '[g]' => $bgColorG, '[b]' => $bgColorB ) ) );
			}

			$textColorR					=	mt_rand( ( ( $imgTxtColors[0] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[0] - $colorRange ) ), ( ( $imgTxtColors[0] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[0] + $colorRange ) ) );
			$textColorG					=	mt_rand( ( ( $imgTxtColors[1] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[1] - $colorRange ) ), ( ( $imgTxtColors[1] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[1] + $colorRange ) ) );
			$textColorB					=	mt_rand( ( ( $imgTxtColors[2] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[2] - $colorRange ) ), ( ( $imgTxtColors[2] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[2] + $colorRange ) ) );

			$textColor					=	imagecolorallocate( $image, $textColorR, $textColorG, $textColorB );

			if ( $textColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_COLOR_FAILED', 'imagecolorallocate failed to set text colors [r], [g], [b]', array( '[r]' => $textColorR, '[g]' => $textColorG, '[b]' => $textColorB ) ) );
			}

			$noiseBgColorR				=	mt_rand( ( ( $imgNoiseBgColors[0] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[0] - $colorRange ) ), ( ( $imgNoiseBgColors[0] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[0] + $colorRange ) ) );
			$noiseBgColorG				=	mt_rand( ( ( $imgNoiseBgColors[1] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[1] - $colorRange ) ), ( ( $imgNoiseBgColors[1] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[1] + $colorRange ) ) );
			$noiseBgColorB				=	mt_rand( ( ( $imgNoiseBgColors[2] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[2] - $colorRange ) ), ( ( $imgNoiseBgColors[2] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[2] + $colorRange ) ) );

			$noiseBgColor				=	imagecolorallocate( $image, $noiseBgColorR, $noiseBgColorG, $noiseBgColorB );

			if ( $noiseBgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise BG colors [r], [g], [b]', array( '[r]' => $noiseBgColorR, '[g]' => $noiseBgColorG, '[b]' => $noiseBgColorB ) ) );
			}

			$noiseFgColorR				=	mt_rand( ( ( $imgNoiseFgColors[0] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[0] - $colorRange ) ), ( ( $imgNoiseFgColors[0] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[0] + $colorRange ) ) );
			$noiseFgColorG				=	mt_rand( ( ( $imgNoiseFgColors[1] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[1] - $colorRange ) ), ( ( $imgNoiseFgColors[1] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[1] + $colorRange ) ) );
			$noiseFgColorB				=	mt_rand( ( ( $imgNoiseFgColors[2] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[2] - $colorRange ) ), ( ( $imgNoiseFgColors[2] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[2] + $colorRange ) ) );

			$noiseFgColor				=	imagecolorallocate( $image, $noiseFgColorR, $noiseFgColorG, $noiseFgColorB );

			if ( $noiseFgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_FG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise FG colors [r], [g], [b]', array( '[r]' => $noiseFgColorR, '[g]' => $noiseFgColorG, '[b]' => $noiseFgColorB ) ) );
			}
		} else {
			if ( imagecolorallocate( $image, $imgBgColors[0], $imgBgColors[1], $imgBgColors[2] ) === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_COLOR_FAILED', 'imagecolorallocate failed to set BG colors [r], [g], [b]', array( '[r]' => $imgBgColors[0], '[g]' => $imgBgColors[1], '[b]' => $imgBgColors[2] ) ) );
			}

			$textColor					=	imagecolorallocate( $image, $imgTxtColors[0], $imgTxtColors[1], $imgTxtColors[2] );

			if ( $textColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_COLOR_FAILED', 'imagecolorallocate failed to set text colors [r], [g], [b]', array( '[r]' => $imgTxtColors[0], '[g]' => $imgTxtColors[1], '[b]' => $imgTxtColors[2] ) ) );
			}

			$noiseBgColor				=	imagecolorallocate( $image, $imgNoiseBgColors[0], $imgNoiseBgColors[1], $imgNoiseBgColors[2] );

			if ( $noiseBgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise BG colors [r], [g], [b]', array( '[r]' => $imgNoiseBgColors[0], '[g]' => $imgNoiseBgColors[1], '[b]' => $imgNoiseBgColors[2] ) ) );
			}

			$noiseFgColor				=	imagecolorallocate( $image, $imgNoiseFgColors[0], $imgNoiseFgColors[1], $imgNoiseFgColors[2] );

			if ( $noiseFgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_FG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise FG colors [r], [g], [b]', array( '[r]' => $imgNoiseFgColors[0], '[g]' => $imgNoiseFgColors[1], '[b]' => $imgNoiseFgColors[2] ) ) );
			}
		}

		if ( $this->params->get( 'captcha_internal_bg_noise', 1 ) ) {
			for ( $i = 0; $i < ( ( $width * $height ) / 3 ); $i++ ) {
				if ( imagefilledellipse( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), 1, 1, $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imagefilledellipse failed to add BG noise to the image' ) );
				}
			}

			for( $i = 1; ( $i < ( $width / 10 ) ); $i++ ) {
				if ( imageline( $image, ( ( $i * 10 ) - mt_rand( -20, 20 ) ), 0, ( ( $i * 10 ) + mt_rand( -20, 20 ) ), $height, $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imageline failed to add first pass BG noise to the image' ) );
				}
			}

			for( $i = 1; ( $i < ( $height / 10 ) ); $i++ ) {
				if ( imageline( $image, 0, ( ( $i * 10 ) - mt_rand( -20, 20 ) ), $width, ( ( $i * 10 ) + mt_rand( -20, 20 ) ), $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imageline failed to add second pass BG noise to the image' ) );
				}
			}
		}

		$x								=	( ( $width - $textBox[4] ) / 2 );
		$y								=	( ( $height - $textBox[5] ) / 2 );
		$codeSplit						=	cbantispamClass::UTF8_str_split( $code );
		$i								=	0;

		if ( $codeSplit ) foreach ( $codeSplit as $c ) {
			$result						=	imagettftext( $image, $fontSize, mt_rand( -$charRotation, $charRotation ), ( $x + $i ), ( $y + mt_rand( -$charOffset, $charOffset ) ), $textColor, $imgFont, $c );

			if ( ! $result ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_FAILED', 'imagettftext failed to add "[text]" to the image', array( '[text]' => $c ) ) );
			}

			$i							+=	( $textBox[4] / count( $codeSplit ) );
		}

		if ( $this->params->get( 'captcha_internal_fg_noise', 1 ) ) for ( $i = 0; $i < 3; $i++ ) {
			if ( imageline( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), mt_rand( 0, $width ), mt_rand( 0, $height ), $noiseFgColor ) === false ) {
				exit( CBTxt::T( 'imageline failed to add FG noise to the image' ) );
			}
		}

		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		switch ( $this->params->get( 'captcha_internal_format', 'jpeg' ) ) {
			case 'gif':
				header( 'Content-Type: image/gif; charset=utf-8' );

				if ( imagegif( $image ) === false ) {
					exit( CBTxt::T( 'imagegif failed to generate image' ) );
				}
				break;
			case 'png':
				header( 'Content-Type: image/png; charset=utf-8' );

				if ( imagepng( $image, null, 0 ) === false ) {
					exit( CBTxt::T( 'imagepng failed to generate image' ) );
				}
				break;
			case 'jpeg':
			default:
				header( 'Content-Type: image/jpeg; charset=utf-8' );

				if ( imagejpeg( $image, null, 100 ) === false ) {
					exit( CBTxt::T( 'imagejpeg failed to generate image' ) );
				}
				break;
		}

		if ( imagedestroy( $image ) === false ) {
			exit( CBTxt::T( 'imagedestroy failed to destroy the image' ) );
		}

		@ob_flush();
		flush();

		exit();
	}

	/**
	 * Generates a captcha audio file
	 *
	 * @param string $id
	 */
	public function captchaAudio( $id )
	{
		global $_PLUGINS;

		if ( ! $id ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		while ( @ob_end_clean() );

		$absPath			=	$_PLUGINS->getPluginPath( $this->getPluginId() );
		$captcha			=	cbantispamCaptcha::getInstance( $id );
		$code				=	$captcha->getCaptchaCode();
		$sounds				=	array();

		for( $i = 0; $i < cbIsoUtf_strlen( $code ); $i++ ) {
			$file			=	$absPath . '/audio/' . $code{$i} . '.mp3';

			if ( ! file_exists( $file ) ) {
				exit( CBTxt::T( 'CAPTCHA_AUDIO_FILE_FAILED', 'failed to locate "[file]" audio file', array( '[file]' => $file ) ) );
			}

			$sounds[]		=	$file;
		}

		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
		header( 'Content-Type: audio/mpeg' );
		header( 'Content-Disposition: inline; filename=cbcaptcha.mp3;' );
		header( 'Content-Transfer-Encoding: binary' );

		$out				=	'';
		$count				=	count( $sounds );
		$i					=	0;

		foreach ( $sounds as $sound ) {
			$i++;

			if ( $i != $count ) {
				$offset		=	128;
			} else {
				$offset		=	0;
			}

			$fh				=	fopen( $sound, 'rb' );
			$size			=	filesize( $sound );

			$out			.=	fread( $fh, ( $size - $offset ) );

			fclose( $fh );
		}

		header( 'Content-Length: ' . cbIsoUtf_strlen( $out ) );

		echo $out;

		@ob_flush();
		flush();

		exit();
	}

	/**
	 * Displays block user page
	 *
	 * @param int         $id
	 * @param string      $type
	 * @param UserTable   $user
	 * @param null|string $message
	 * @param null|string $messageType
	 */
	public function showBlock( $id, $type, $user, $message = null, $messageType = 'error' )
	{
		global $_CB_framework;

		$profileUrl				=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $user->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$ipAddress				=	cbantispamClass::getUserIP( $user );
		$value					=	null;

		switch ( $type ) {
			case 'user':
				$value			=	(int) $user->get( 'id' );
				break;
			case 'ip':
				$value			=	$ipAddress;
				break;
			case 'email':
				$value			=	$user->get( 'email' );
				break;
			case 'domain':
				$emailParts		=	explode( '@', $user->get( 'email' ) );

				if ( count( $emailParts ) > 1 ) {
					$value		=	array_pop( $emailParts );
				}
				break;
		}

		$row					=	new cbantispamBlockTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		$js						=	"$( '#durations' ).on( 'change', function() {"
								.		"var value = $( this ).val();"
								.		"if ( value ) {"
								.			"$( '#duration' ).attr( 'value', value ).focus();"
								.			"$( this ).attr( 'value', '' );"
								.		"}"
								.	"});"
								.	"$( '#type' ).on( 'change', function() {"
								.		"if ( $( this ).val() == 'user' ) {"
								.			"$( '#banUsr,#blockUsr' ).show();"
								.			"$( '#ban_user' ).trigger( 'change' );"
								.		"} else {"
								.			"$( '#banUsr,#blockUsr,#banUsrReason' ).hide();"
								.		"}"
								.	"});"
								.	"$( '#ban_user' ).on( 'change', function() {"
								.		"if ( $( this ).val() == 1 ) {"
								.			"$( '#banUsrReason' ).show();"
								.		"} else {"
								.			"$( '#banUsrReason' ).hide();"
								.		"}"
								.	"});"
								.	"$( '#type' ).change();";

		$_CB_framework->outputCbJQuery( $js );

		cbantispamClass::getTemplate( 'block' );

		$input					=	array();

		$listType				=	array();
		$listType[]				=	moscomprofilerHTML::makeOption( 'user', CBTxt::T( 'User' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip', CBTxt::T( 'IP Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'email', CBTxt::T( 'Email Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'domain', CBTxt::T( 'Email Domain' ) );

		$type					=	$this->input( 'post/type', $row->get( 'type', $type ), GetterInterface::STRING );
		$typeTooltip			=	cbTooltip( null, CBTxt::T( 'Select the block type. Type determines what value should be supplied.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['type']			=	moscomprofilerHTML::selectList( $listType, 'type', 'class="form-control required"' . ( $typeTooltip ? ' ' . $typeTooltip : null ), 'value', 'text', $type, 1, true, false, false );

		$valueTooltip			=	cbTooltip( null, CBTxt::T( 'Input block value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com).' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['value']			=	'<input type="text" id="value" name="value" value="' . htmlspecialchars( $this->input( 'post/value', $row->get( 'value', $value ), GetterInterface::STRING ) ) . '" class="form-control required" size="25"' . ( $valueTooltip ? ' ' . $valueTooltip : null ) . ' />';

		$calendar				=	new cbCalendars( 1 );
		$dateTooltip			=	cbTooltip( null, CBTxt::T( 'Select the date and time the block should go in affect. Note date and time always functions in UTC.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['date']			=	$calendar->cbAddCalendar( 'date', null, true, $this->input( 'post/date', $row->get( 'date' ), GetterInterface::STRING ), false, true, null, null, $dateTooltip );

		$durationTooltip		=	cbTooltip( null, CBTxt::T( 'Input the strtotime relative date (e.g. +1 Day). This duration will be added to the datetime specified above. Leave blank for a forever duration.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['duration']		=	'<input type="text" id="duration" name="duration" value="' . htmlspecialchars( $this->input( 'post/duration', $row->get( 'duration' ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . ( $durationTooltip ? ' ' . $durationTooltip : null ) . ' />';

		$listDurations			=	array();
		$listDurations[]		=	moscomprofilerHTML::makeOption( '', CBTxt::T( '- Select Duration -' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'MIDNIGHT', CBTxt::T( 'Midnight' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NOON', CBTxt::T( 'Noon' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'TOMORROW', CBTxt::T( 'Tomorrow' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT WEEK', CBTxt::T( 'Next Week' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT MONTH', CBTxt::T( 'Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT YEAR', CBTxt::T( 'Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF THIS MONTH', CBTxt::T( 'Last Day of This Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NEXT MONTH', CBTxt::T( 'First Day of Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NEXT MONTH', CBTxt::T( 'Last Day of Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF THIS YEAR', CBTxt::T( 'Last Day of This Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NEXT YEAR', CBTxt::T( 'First Day of Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NEXT YEAR', CBTxt::T( 'Last Day of Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JANUARY', CBTxt::T( 'First Day of January' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JANUARY', CBTxt::T( 'Last Day of January' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF FEBRUARY', CBTxt::T( 'First Day of February' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF FEBRUARY', CBTxt::T( 'Last Day of February' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF MARCH', CBTxt::T( 'First Day of March' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF MARCH', CBTxt::T( 'Last Day of March' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF APRIL', CBTxt::T( 'First Day of Apil' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF APRIL', CBTxt::T( 'Last Day of Apil' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF MAY', CBTxt::T( 'First Day of May' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF MAY', CBTxt::T( 'Last Day of May' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JUNE', CBTxt::T( 'First Day of June' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JUNE', CBTxt::T( 'Last Day of June' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JULY', CBTxt::T( 'First Day of July' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JULY', CBTxt::T( 'Last Day of July' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF AUGUST', CBTxt::T( 'First Day of August' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF AUGUST', CBTxt::T( 'Last Day of August' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF SEPTEMBER', CBTxt::T( 'First Day of September' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF SEPTEMBER', CBTxt::T( 'Last Day of September' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF OCTOBER', CBTxt::T( 'First Day of October' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF OCTOBER', CBTxt::T( 'Last Day of October' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NOVEMBER', CBTxt::T( 'First Day of November' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NOVEMBER', CBTxt::T( 'Last Day of November' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF DECEMBER', CBTxt::T( 'First Day of December' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF DECEMBER', CBTxt::T( 'Last Day of December' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+30 MINUTES', CBTxt::T( '30 Minutes' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 HOUR', CBTxt::T( '1 Hour' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 HOURS', CBTxt::T( '3 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 HOURS', CBTxt::T( '6 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+12 HOURS', CBTxt::T( '12 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 DAY', CBTxt::T( '1 Day' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 DAYS', CBTxt::T( '3 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 DAYS', CBTxt::T( '6 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+12 DAYS', CBTxt::T( '12 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 WEEK', CBTxt::T( '1 Week' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+2 WEEKS', CBTxt::T( '2 Weeks' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 WEEKS', CBTxt::T( '3 Weeks' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 MONTH', CBTxt::T( '1 Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 MONTHS', CBTxt::T( '3 Months' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 MONTHS', CBTxt::T( '6 Months' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 YEAR', CBTxt::T( '1 Year' ) );
		$input['durations']		=	moscomprofilerHTML::selectList( $listDurations, 'durations', 'class="form-control"', 'value', 'text', null, 0, true, false, false );

		$reasonTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input block reason. If left blank will default to spam.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['reason']		=	'<textarea id="reason" name="reason" class="form-control" cols="40" rows="5"' . ( $reasonTooltip ? ' ' . $reasonTooltip : null ) . '>' . htmlspecialchars( $this->input( 'post/reason', $row->get( 'reason' ), GetterInterface::STRING ) ) . '</textarea>';

		$banUserTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally ban the users profile using Community Builder moderator ban feature. Note normal ban notification will be sent with the ban.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['ban_user']		=	moscomprofilerHTML::yesnoSelectList( 'ban_user', 'class="form-control"' . ( $banUserTooltip ? ' ' . $banUserTooltip : null ), $this->input( 'post/ban_user', 0, GetterInterface::INT ) );

		$banReasonTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally input reason for profile ban.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['ban_reason']	=	'<textarea id="ban_reason" name="ban_reason" class="form-control" cols="40" rows="5"' . ( $banReasonTooltip ? ' ' . $banReasonTooltip : null ) . '>' . htmlspecialchars( $this->input( 'post/ban_reason', null, GetterInterface::STRING ) ) . '</textarea>';

		$blockUserTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally block the users profile using Joomla block state.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['block_user']	=	moscomprofilerHTML::yesnoSelectList( 'block_user', 'class="form-control"' . ( $blockUserTooltip ? ' ' . $blockUserTooltip : null ), $this->input( 'post/block_user', 0, GetterInterface::INT ) );

		if ( $message ) {
			$_CB_framework->enqueueMessage( $message, $messageType );
		}

		HTML_cbantispamBlock::showBlock( $row, $input, $type, $this->_tab, $user, $this );
	}

	/**
	 * Saves a user block
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveBlock( $id, $user )
	{
		global $_CB_framework, $ueConfig;

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $user->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row			=	new cbantispamBlockTable();

		$row->load( (int) $id );

		$row->set( 'type', $this->input( 'type', $row->get( 'type' ), GetterInterface::STRING ) );
		$row->set( 'value', $this->input( 'value', $row->get( 'value' ), GetterInterface::STRING ) );
		$row->set( 'reason', $this->input( 'reason', $row->get( 'reason' ), GetterInterface::STRING ) );
		$row->set( 'date', $this->input( 'date', $row->get( 'date', '0000-00-00 00:00:00' ), GetterInterface::STRING ) );
		$row->set( 'duration', $this->input( 'duration', $row->get( 'duration' ), GetterInterface::STRING ) );

		if ( $row->get( 'type' ) == '' ) {
			$row->setError( CBTxt::T( 'Type not specified!' ) );
		} elseif ( $row->get( 'value' ) == '' ) {
			$row->setError( CBTxt::T( 'Value not specified!' ) );
		} elseif ( ( $row->get( 'date' ) == '' ) || ( $row->get( 'date' ) == '0000-00-00 00:00:00' ) ) {
			$row->setError( CBTxt::T( 'Date not specified!' ) );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$this->showBlock( $id, $row->get( 'type' ), $user, CBTxt::T( 'BLOCK_SAVE_FAILED', 'Block failed to save! Error: [error]', array( '[error]' => $row->getError() ) ) );
			return;
		}

		if ( $row->get( 'type' ) == 'user' ) {
			if ( isset( $ueConfig['allowUserBanning'] ) && $ueConfig['allowUserBanning'] ) {
				if ( $this->input( 'ban_user', 0, GetterInterface::INT ) && ( ! $user->get( 'banned' ) ) ) {
					if ( ! $user->banUser( 1, null, $this->input( 'ban_reason', null, GetterInterface::STRING ) ) ) {
						$this->showBlock( $id, $row->get( 'type' ), $user, CBTxt::T( 'BLOCK_PROFILE_BAN_FAILED', 'Block saved successfully, but Profile Ban failed to save! Error: [error]', array( '[error]' => $user->getError() ) ) );
						return;
					}
				}
			}

			if ( $this->input( 'block_user', 0, GetterInterface::INT ) && ( ! $user->get( 'block' ) ) ) {
				$user->set( 'block', 1 );

				if ( ! $user->storeBlock() ) {
					$this->showBlock( $id, $row->get( 'type' ), $user, CBTxt::T( 'BLOCK_PROFILE_BLOCK_FAILED', 'Block saved successfully, but Profile Block failed to save! Error: [error]', array( '[error]' => $user->getError() ) ) );
					return;
				}
			}
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Block saved successfully!' ) );
	}

	/**
	 * Deletes a user block
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteBlock( $id, $user )
	{
		global $_CB_framework;

		$row			=	new cbantispamBlockTable();

		$row->load( (int) $id );

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $row->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $profileUrl, CBTxt::T( 'BLOCK_DELETE_FAILED', 'Block failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Block deleted successfully!' ) );
	}

	/**
	 * Displays whitelist user page
	 *
	 * @param int         $id
	 * @param string      $type
	 * @param UserTable   $user
	 * @param null|string $message
	 * @param null|string $messageType
	 */
	public function showWhitelist( $id, $type, $user, $message = null, $messageType = 'error' )
	{
		global $_CB_framework;

		$profileUrl				=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $user->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$ipAddress				=	cbantispamClass::getUserIP( $user );
		$value					=	null;

		switch ( $type ) {
			case 'user':
				$value			=	(int) $user->get( 'id' );
				break;
			case 'ip':
				$value			=	$ipAddress;
				break;
			case 'email':
				$value			=	$user->get( 'email' );
				break;
			case 'domain':
				$emailParts		=	explode( '@', $user->get( 'email' ) );

				if ( count( $emailParts ) > 1 ) {
					$value		=	array_pop( $emailParts );
				}
				break;
		}

		$row					=	new cbantispamWhitelistTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		cbantispamClass::getTemplate( 'whitelist' );

		$input					=	array();

		$listType				=	array();
		$listType[]				=	moscomprofilerHTML::makeOption( 'user', CBTxt::T( 'User' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip', CBTxt::T( 'IP Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'email', CBTxt::T( 'Email Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'domain', CBTxt::T( 'Email Domain' ) );

		$type					=	$this->input( 'post/type', $row->get( 'type', $type ), GetterInterface::STRING );
		$typeTooltip			=	cbTooltip( null, CBTxt::T( 'Select whitelist block type. Type determines what value should be supplied.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['type']			=	moscomprofilerHTML::selectList( $listType, 'type', 'class="form-control required"' . ( $typeTooltip ? ' ' . $typeTooltip : null ), 'value', 'text', $type, 1, true, false, false );

		$valueTooltip			=	cbTooltip( null, CBTxt::T( 'Input whitelist value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com).' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['value']			=	'<input type="text" id="value" name="value" value="' . htmlspecialchars( $this->input( 'post/value', $row->get( 'value', $value ), GetterInterface::STRING ) ) . '" class="form-control required" size="25"' . ( $valueTooltip ? ' ' . $valueTooltip : null ) . ' />';

		$reasonTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input whitelist reason. Note this is for administrative purposes only.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['reason']		=	'<textarea id="reason" name="reason" class="form-control" cols="40" rows="5"' . ( $reasonTooltip ? ' ' . $reasonTooltip : null ) . '>' . htmlspecialchars( $this->input( 'post/reason', $row->get( 'reason' ), GetterInterface::STRING ) ) . '</textarea>';

		if ( $message ) {
			$_CB_framework->enqueueMessage( $message, $messageType );
		}

		HTML_cbantispamWhitelist::showWhitelist( $row, $input, $type, $this->_tab, $user, $this );
	}

	/**
	 * Saves a user whitelist
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveWhitelist( $id, $user )
	{
		global $_CB_framework;

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $user->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row			=	new cbantispamWhitelistTable();

		$row->load( (int) $id );

		$row->set( 'type', $this->input( 'type', $row->get( 'type' ), GetterInterface::STRING ) );
		$row->set( 'value', $this->input( 'value', $row->get( 'value' ), GetterInterface::STRING ) );
		$row->set( 'reason', $this->input( 'reason', $row->get( 'reason' ), GetterInterface::STRING ) );

		if ( $row->get( 'type' ) == '' ) {
			$row->setError( CBTxt::T( 'Type not specified!' ) );
		} elseif ( $row->get( 'value' ) == '' ) {
			$row->setError( CBTxt::T( 'Value not specified!' ) );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$this->showBlock( $id, $row->get( 'type' ), $user, CBTxt::T( 'WHITELIST_SAVE_FAILED', 'Whitelist failed to save! Error: [error]', array( '[error]' => $row->getError() ) ) );
			return;
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Whitelist saved successfully!' ) );
	}

	/**
	 * Deletes a user whitelist
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteWhitelist( $id, $user )
	{
		global $_CB_framework;

		$row			=	new cbantispamWhitelistTable();

		$row->load( (int) $id );

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $row->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $profileUrl, CBTxt::T( 'WHITELIST_DELETE_FAILED', 'Whitelist failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Whitelist deleted successfully!' ) );
	}

	/**
	 * Deletes a user attempt
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteAttempt( $id, $user )
	{
		global $_CB_framework;

		$row			=	new cbantispamAttemptsTable();

		$row->load( (int) $id );

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $row->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $profileUrl, CBTxt::T( 'ATTEMPT_DELETE_FAILED', 'Attempt failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Attempt deleted successfully!' ) );
	}

	/**
	 * Deletes a user log
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteLog( $id, $user )
	{
		global $_CB_framework;

		$row			=	new cbantispamLogTable();

		$row->load( (int) $id );

		$profileUrl		=	$_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $this->_tab );

		if ( ! $row->get( 'id' ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $profileUrl, CBTxt::T( 'LOG_DELETE_FAILED', 'Log failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Log deleted successfully!' ) );
	}
}