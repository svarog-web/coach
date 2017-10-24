<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionK2 extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_database;

		if ( ! $user->get( 'id' ) ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_K2_NO_USER', ':: Action [action] :: K2 skipped due to no user', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_K2_NOT_INSTALLED', ':: Action [action] :: K2 is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		$params				=	$trigger->getParams()->subTree( 'k2' );

		if ( $params->get( 'mode', 1, GetterInterface::INT ) ) {
			$group			=	(int) $params->get( 'group', null, GetterInterface::INT );
			$gender			=	$trigger->getSubstituteString( $params->get( 'gender', null, GetterInterface::STRING ) );
			$description	=	$trigger->getSubstituteString( $params->get( 'description', null, GetterInterface::RAW ), false );
			$url			=	$trigger->getSubstituteString( $params->get( 'url', null, GetterInterface::STRING ) );
			$notes			=	$trigger->getSubstituteString( $params->get( 'notes', null, GetterInterface::RAW ), false );

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__k2_users' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userID' ) . " = " . (int) $user->get( 'id' );
			$_CB_database->setQuery( $query, 0, 1 );
			$k2User			=	null;
			$_CB_database->loadObject( $k2User );

			if ( $k2User ) {
				$set		=	array();

				if ( $group && ( $k2User->group != $group ) ) {
					$set[]	=	$_CB_database->NameQuote( 'group' ) . " = " . $_CB_database->Quote( $group );
				}

				if ( $gender && ( $k2User->gender != $gender ) ) {
					$set[]	=	$_CB_database->NameQuote( 'gender' ) . " = " . $_CB_database->Quote( $gender );
				}

				if ( $description && ( $k2User->description != $description ) ) {
					$set[]	=	$_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $description );
				}

				if ( $url && ( $k2User->url != $url ) ) {
					$set[]	=	$_CB_database->NameQuote( 'url' ) . " = " . $_CB_database->Quote( $url );
				}

				if ( $notes && ( $k2User->notes != $notes ) ) {
					$set[]	=	$_CB_database->NameQuote( 'notes' ) . " = " . $_CB_database->Quote( $notes );
				}

				if ( ! empty( $set ) ) {
					$query	=	'UPDATE ' . $_CB_database->NameQuote( '#__k2_users' )
							.	"\n SET " . implode( ', ', $set )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userID' ) . " = " . (int) $user->get( 'id' );
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
			} else {
				$ip			=	$this->input( 'server/REMOTE_ADDR', null, GetterInterface::STRING );
				$hostname	=	gethostbyaddr( $ip );

				$query		=	'INSERT INTO ' . $_CB_database->NameQuote( '#__k2_users' )
							.	"\n ("
							.		$_CB_database->NameQuote( 'userID' )
							.		', ' . $_CB_database->NameQuote( 'userName' )
							.		', ' . $_CB_database->NameQuote( 'gender' )
							.		', ' . $_CB_database->NameQuote( 'description' )
							.		', ' . $_CB_database->NameQuote( 'url' )
							.		', ' . $_CB_database->NameQuote( 'group' )
							.		', ' . $_CB_database->NameQuote( 'ip' )
							.		', ' . $_CB_database->NameQuote( 'hostname' )
							.		', ' . $_CB_database->NameQuote( 'notes' )
							.	')'
							.	"\n VALUES ("
							.		(int) $user->get( 'id' )
							.		', ' . $_CB_database->Quote( $user->get( 'username' ) )
							.		', ' . $_CB_database->Quote( ( $gender ? $gender : 'm' ) )
							.		', ' . $_CB_database->Quote( $description )
							.		', ' . $_CB_database->Quote( $url )
							.		', ' . ( $group ? $group : 1 )
							.		', ' . $_CB_database->Quote( $ip )
							.		', ' . $_CB_database->Quote( $hostname )
							.		', ' . $_CB_database->Quote( $notes )
							.	')';
				$_CB_database->setQuery( $query );
				$_CB_database->query();

				$query		=	'DELETE'
							.	"\n FROM " . $_CB_database->NameQuote( '#__k2_users' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userID' ) . " = 0"
							.	"\n AND " . $_CB_database->NameQuote( 'ip' ) . " = " . $_CB_database->Quote( $ip )
							.	"\n AND " . $_CB_database->NameQuote( 'hostname' ) . " = " . $_CB_database->Quote( $hostname );
				$_CB_database->setQuery( $query );
				$_CB_database->query();
			}
		} else {
			$query			=	'DELETE'
							.	"\n FROM " . $_CB_database->NameQuote( '#__k2_users' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userID' ) . " = " . (int) $user->get( 'id' );
			$_CB_database->setQuery( $query );
			$_CB_database->query();
		}
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework;

		if ( is_dir( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2' ) && class_exists( 'K2Model' ) ) {
			return true;
		}

		return false;
	}
}