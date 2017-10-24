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
use CBLib\Database\Driver\CmsDatabaseDriver;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionQuery extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework, $_CB_database;

		foreach ( $trigger->getParams()->subTree( 'query' ) as $row ) {
			/** @var ParamsInterface $row */
			if ( $row->get( 'mode', 0, GetterInterface::BOOLEAN ) ) {
				$host					=	$row->get( 'host', null, GetterInterface::STRING );

				if ( ! $host ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_NO_HOST', ':: Action [action] :: Query skipped due to missing host', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$username				=	$row->get( 'username', null, GetterInterface::STRING );

				if ( ! $username ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_NO_USERNAME', ':: Action [action] :: Query skipped due to missing username', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$password				=	$row->get( 'password', null, GetterInterface::STRING );

				if ( ! $password ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_NO_PSWD', ':: Action [action] :: Query skipped due to missing password', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$database				=	$row->get( 'database', null, GetterInterface::STRING );

				if ( ! $database ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_NO_DB', ':: Action [action] :: Query skipped due to missing database', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$charset				=	$row->get( 'charset', null, GetterInterface::STRING );
				$prefix					=	$row->get( 'prefix', null, GetterInterface::STRING );

				if ( ! $prefix ) {
					$prefix				=	$_CB_framework->getCfg( 'dbprefix' );
				}

				$driver					=	$_CB_framework->getCfg( 'dbtype' );
				$options				=	array ( 'driver' => $driver, 'host' => $host, 'user' => $username, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

				if ( is_callable( array( 'JDatabaseDriver', 'getInstance' ) ) ) {
					try {
						$_J_database	=&	JDatabaseDriver::getInstance( $options );
					} catch ( RuntimeException $e ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_EXT_DB_FAILED', ':: Action [action] :: Query external database failed. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $e->getMessage() ) ) );
						}

						continue;
					}
				} else {
					try {
						$_J_database	=&	JDatabase::getInstance( $options );
					} catch ( RuntimeException $e ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_QUERY_EXT_DB_FAILED', ':: Action [action] :: Query external database failed. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $e->getMessage() ) ) );
						}

						continue;
					}
				}

				$_SQL_database			=	new CmsDatabaseDriver( $_J_database, $prefix, checkJversion( 'release' ) );

				if ( $charset ) {
					$_SQL_database->setQuery( 'SET NAMES ' . $_SQL_database->Quote( $charset ) );
					$_SQL_database->query();
				}
			} else {
				$_SQL_database			=	$_CB_database;
			}

			if ( $_SQL_database ) {
				$queries				=	preg_split( '/(;\s*[\r\n])/', trim( $trigger->getSubstituteString( $row->get( 'sql', null, GetterInterface::RAW ), array( 'cbautoactionsClass', 'escapeSQL' ) ) ) );

				if ( $queries ) foreach ( $queries as $query ) {
					if ( $query ) {
						$_SQL_database->setQuery( $query );
						$_SQL_database->query();
					}
				}
			}
		}
	}
}