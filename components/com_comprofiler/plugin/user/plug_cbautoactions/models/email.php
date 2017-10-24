<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionEmail extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_database;

		$moderators										=	Application::CmsPermissions()->getGroupsOfViewAccessLevel( Application::Config()->get( 'moderator_viewaccesslevel', 3, GetterInterface::INT ), true );

		if ( $moderators ) {
			$query										=	'SELECT DISTINCT u.' . $_CB_database->NameQuote( 'email' )
														.	"\n FROM " . $_CB_database->NameQuote( '#__users' ) . " AS u"
														.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
														.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
														.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__user_usergroup_map' ) . " AS g"
														.	' ON g.' . $_CB_database->NameQuote( 'user_id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
														.	"\n WHERE g." . $_CB_database->NameQuote( 'group_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $moderators )
														.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
														.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
														.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
														.	"\n AND u." . $_CB_database->NameQuote( 'sendEmail' ) . " = 1";

			$_CB_database->setQuery( $query );
			$mods										=	$_CB_database->loadResultArray();

			if ( $mods ) {
				$trigger->set( '_extras', array_merge( $trigger->get( '_extras' ), array( 'cb_moderators' => implode( ',', $mods ) ) ) );
			}
		}

		foreach ( $trigger->getParams()->subTree( 'email' ) as $row ) {
			/** @var ParamsInterface $row */
			$mailTo										=	$row->get( 'to', null, GetterInterface::STRING );

			if ( ! $mailTo ) {
				$mailTo									=	$user->get( 'email', null, GetterInterface::STRING );
			} else {
				$mailTo									=	$trigger->getSubstituteString( $mailTo );
			}

			if ( ! $mailTo ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_EMAIL_NO_TO', ':: Action [action] :: Email skipped due to missing to', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$mailSubject								=	$trigger->getSubstituteString( $row->get( 'subject', null, GetterInterface::STRING ) );

			if ( ! $mailSubject ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_EMAIL_NO_SBJ', ':: Action [action] :: Email skipped due to missing subject', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$mailBody									=	$trigger->getSubstituteString( $row->get( 'body', null, GetterInterface::RAW ), false );

			if ( ! $mailBody ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_EMAIL_NO_BODY', ':: Action [action] :: Email skipped due to missing body', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$mailHtml									=	(int) $row->get( 'mode', null, GetterInterface::INT );
			$mailCC										=	$trigger->getSubstituteString( $row->get( 'cc', null, GetterInterface::STRING ) );
			$mailBCC									=	$trigger->getSubstituteString( $row->get( 'bcc', null, GetterInterface::STRING ) );
			$mailAttachments							=	$trigger->getSubstituteString( $row->get( 'attachment', null, GetterInterface::STRING ) );
			$mailReplyToEmail							=	$trigger->getSubstituteString( $row->get( 'replyto_address', null, GetterInterface::STRING ) );
			$mailReplyToName							=	$trigger->getSubstituteString( $row->get( 'replyto_name', null, GetterInterface::STRING ) );
			$mailFromEmail								=	$trigger->getSubstituteString( $row->get( 'from_address', null, GetterInterface::STRING ) );
			$mailFromName								=	$trigger->getSubstituteString( $row->get( 'from_name', null, GetterInterface::STRING ) );
			$mailMailer									=	$row->get( 'mailer', null, GetterInterface::STRING );
			$mailProperties								=	array();

			if ( $mailTo ) {
				$mailTo									=	preg_split( '/ *, */', $mailTo );

				foreach ( $mailTo as $k => $mailToEmail ) {
					if ( is_numeric( $mailToEmail ) ) {
						$mailTo[$k]						=	CBuser::getUserDataInstance( (int) $mailToEmail )->get( 'email', null, GetterInterface::STRING );
					}
				}
			} else {
				$mailTo									=	null;

				if ( is_numeric( $mailTo ) ) {
					$mailTo								=	CBuser::getUserDataInstance( (int) $mailTo )->get( 'email', null, GetterInterface::STRING );
				}
			}

			if ( $mailCC ) {
				$mailCC									=	preg_split( '/ *, */', $mailCC );

				foreach ( $mailCC as $k => $mailCCEmail ) {
					if ( is_numeric( $mailCCEmail ) ) {
						$mailCC[$k]						=	CBuser::getUserDataInstance( (int) $mailCCEmail )->get( 'email', null, GetterInterface::STRING );
					}
				}
			} else {
				$mailCC									=	null;

				if ( is_numeric( $mailCC ) ) {
					$mailCC								=	CBuser::getUserDataInstance( (int) $mailCC )->get( 'email', null, GetterInterface::STRING );
				}
			}

			if ( $mailBCC ) {
				$mailBCC								=	preg_split( '/ *, */', $mailBCC );

				foreach ( $mailBCC as $k => $mailBCCEmail ) {
					if ( is_numeric( $mailBCCEmail ) ) {
						$mailBCC[$k]					=	CBuser::getUserDataInstance( (int) $mailBCCEmail )->get( 'email', null, GetterInterface::STRING );
					}
				}
			} else {
				$mailBCC								=	null;

				if ( is_numeric( $mailBCC ) ) {
					$mailBCC							=	CBuser::getUserDataInstance( (int) $mailBCC )->get( 'email', null, GetterInterface::STRING );
				}
			}

			if ( $mailAttachments ) {
				$mailAttachments						=	preg_split( '/ *, */', $mailAttachments );
			} else {
				$mailAttachments						=	null;
			}

			if ( $mailReplyToEmail ) {
				$mailReplyToEmail						=	preg_split( '/ *, */', $mailReplyToEmail );
			} else {
				$mailReplyToEmail						=	null;
			}

			if ( $mailReplyToName ) {
				$mailReplyToName						=	preg_split( '/ *, */', $mailReplyToName );
			} else {
				$mailReplyToName						=	null;
			}

			if ( $mailMailer ) {
				$mailProperties['Mailer']				=	$mailMailer;

				if ( $mailMailer == 'smtp' ) {
					$mailProperties['SMTPAuth']			=	(int) $row->get( 'mailer_smtpauth', null, GetterInterface::INT );
					$mailProperties['Username']			=	$row->get( 'mailer_smtpuser', null, GetterInterface::STRING );
					$mailProperties['Password']			=	$row->get( 'mailer_smtppass', null, GetterInterface::STRING );
					$mailProperties['Host']				=	$row->get( 'mailer_smtphost', null, GetterInterface::STRING );

					$smtpPort							=	(int) $row->get( 'mailer_smtpport', null, GetterInterface::INT );

					if ( $smtpPort ) {
						$mailProperties['Port']			=	$smtpPort;
					}

					$smtpSecure							=	$row->get( 'mailer_smtpsecure', null, GetterInterface::STRING );

					if ( ( $smtpSecure === 'ssl' ) || ( $smtpSecure === 'tls' ) ) {
						$mailProperties['SMTPSecure']	=	$smtpSecure;
					}
				} elseif ( $mailMailer == 'sendmail' ) {
					$sendMail							=	$row->get( 'mailer_sendmail', null, GetterInterface::STRING );

					if ( $sendMail ) {
						$mailProperties['Sendmail']		=	$sendMail;
					}
				}
			}

			$error										=	null;

			if ( ! comprofilerMail( $mailFromEmail, $mailFromName, $mailTo, $mailSubject, $mailBody, $mailHtml, $mailCC, $mailBCC, $mailAttachments, $mailReplyToEmail, $mailReplyToName, $mailProperties, $error ) ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_EMAIL_FAILED', ':: Action [action] :: Email failed to send. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $error ) ) );
				}
			}
		}
	}
}