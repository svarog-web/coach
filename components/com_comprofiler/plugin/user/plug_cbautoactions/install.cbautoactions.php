<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Database\Table\Table;
use CBLib\Xml\SimpleXMLElement;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbautoactions_install()
{
	global $_CB_framework, $_CB_database;

	$table									=	'#__comprofiler_plugin_autoactions';
	$fields									=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['field'] ) ) {
		$translateExists					=	isset( $fields[$table]['translate'] );
		$excludeExists						=	isset( $fields[$table]['exclude'] );
		$debugExists						=	isset( $fields[$table]['debug'] );

		$query								=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' );
		$_CB_database->setQuery( $query );
		$rows								=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__comprofiler_plugin_autoactions', 'id' ) );

		/** @var $rows Table[] */
		foreach ( $rows as $row ) {
			$row->set( 'trigger', str_replace( ',', '|*|', $row->get( 'trigger' ) ) );
			$row->set( 'params', new Registry( $row->get( 'params' ) ) );

			$newParams						=	new Registry();

			if ( $row->get( 'field' ) ) {
				$fields						=	new Registry( $row->get( 'field' ) );
				$operators					=	new Registry( $row->get( 'operator' ) );
				$values						=	new Registry( $row->get( 'value' ) );

				if ( $translateExists ) {
					$translates				=	new Registry( $row->get( 'translate' ) );
				} else {
					$translates				=	null;
				}

				$conditionals				=	count( $fields );

				if ( $conditionals ) {
					$conditions				=	array();

					for ( $i = 0, $n = $conditionals; $i < $n; $i++ ) {
						$field				=	$fields->get( "field$i" );
						$operator			=	$operators->get( "operator$i" );
						$value				=	$values->get( "value$i" );

						if ( $translateExists ) {
							$translate		=	$translates->get( "translate$i" );
						} else {
							$translate		=	0;
						}

						if ( $operator !== '' ) {
							$conditions[]	=	array( 'field' => $field, 'operator' => $operator, 'value' => $value, 'translate' => $translate );
						}
					}

					if ( $conditions ) {
						$newConditionals	=	new Registry( $conditions );

						$row->set( 'conditions', $newConditionals->asJson() );
					}
				}

				$row->set( 'field', null );
				$row->set( 'operator', null );
				$row->set( 'value', null );

				if ( $translateExists ) {
					$row->set( 'translate', null );
				}
			}

			if ( $excludeExists ) {
				$exclude					=	$row->get( 'exclude' );

				if ( $exclude ) {
					$newParams->set( 'exclude', $exclude );
					$row->set( 'exclude', null );
				}
			}

			if ( $debugExists ) {
				$debug						=	$row->get( 'debug' );

				if ( $debug ) {
					$newParams->set( 'debug', $debug );
					$row->set( 'debug', null );
				}
			}

			if ( method_exists( 'cbautoactionsMigrate', $row->get( 'type' ) ) ) {
				call_user_func_array( array( 'cbautoactionsMigrate', $row->get( 'type' ) ), array( &$row, &$newParams ) );
			}

			$row->set( 'params', $newParams->asJson() );

			$row->store( true );
		}

		$_CB_database->dropColumn( $table, 'field' );
		$_CB_database->dropColumn( $table, 'operator' );
		$_CB_database->dropColumn( $table, 'value' );

		if ( $translateExists ) {
			$_CB_database->dropColumn( $table, 'translate' );
		}

		if ( $excludeExists ) {
			$_CB_database->dropColumn( $table, 'exclude' );
		}

		if ( $debugExists ) {
			$_CB_database->dropColumn( $table, 'debug' );
		}
	} else {
		// Convert old |*| delimitered triggers to comma separated:
		$query								=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
											.	"\n SET " . $_CB_database->NameQuote( 'trigger' ) . " = REPLACE( " . $_CB_database->NameQuote( 'trigger' ) . ", " . $_CB_database->Quote( ',' ) . ", " . $_CB_database->Quote( '|*|' ) . " )";
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}

	// Delete system actions that no longer exist:
	if ( isset( $fields[$table]['system'] ) ) {
		$xmlFile							=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbautoactions/cbautoactions.xml';

		if ( file_exists( $xmlFile ) ) {
			$xml							=	new SimpleXMLElement( trim( file_get_contents( $xmlFile ) ) );

			$systemRows						=	$xml->xpath( '//database/table[@name="#__comprofiler_plugin_autoactions"]/rows/row[@index="system"]/@value' );

			if ( $systemRows !== false ) {
				$systemIds					=	array();

				foreach ( $systemRows as $systemRow ) {
					$systemIds[]			=	(string) $systemRow;
				}

				if ( $systemIds ) {
					$query					=	'DELETE'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'system' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $systemIds )
											.	"\n AND " . $_CB_database->NameQuote( 'system' ) . " != 0";
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
			}
		}
	}
}

class cbautoactionsMigrate
{

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function activity( &$trigger, &$params )
	{
		$activityCount						=	substr_count( $trigger->get( 'params' ), 'activity_owner' );

		if ( $activityCount ) {
			$newParams						=	array();
			$newParams['activity']			=	array();

			$paramsMap						=	array(	'activity_owner' => 'owner', 'activity_user' => 'user', 'activity_type' => 'type',
														'activity_subtype' => 'subtype', 'activity_item' => 'item', 'activity_from' => 'from',
														'activity_to' => 'to', 'activity_title' => 'title', 'activity_message' => 'message',
														'activity_icon' => 'icon', 'activity_class' => 'class'
													);

			for ( $i = 0, $n = $activityCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$activity					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$activity[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['activity'][]	=	$activity;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function acymailing( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'acymailing_subscribe' ) ) {
			$newParams							=	array();
			$newParams['acymailing']			=	array();

			$paramsMap							=	array(	'acymailing_subscribe' => 'subscribe', 'acymailing_unsubscribe' => 'unsubscribe',
															'acymailing_remove' => 'remove', 'acymailing_pending' => 'pending'
														);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['acymailing'][$new]	=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function antispam( &$trigger, &$params )
	{
		$antispamCount						=	substr_count( $trigger->get( 'params' ), 'antispam_value' );

		if ( $antispamCount ) {
			$newParams						=	array();
			$newParams['antispam']			=	array();

			$paramsMap						=	array(	'antispam_mode' => 'mode', 'antispam_type' => 'type', 'antispam_value' => 'value',
														'antispam_duration' => 'duration', 'antispam_reason' => 'reason'
													);

			for ( $i = 0, $n = $antispamCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$antispam					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$antispam[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['antispam'][]	=	$antispam;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function cbsubs30( &$trigger, &$params )
	{
		$cbsubsCount						=	substr_count( $trigger->get( 'params' ), 'cbsubs30_plans' );

		if ( $cbsubsCount ) {
			$trigger->set( 'type', 'cbsubs' );

			$newParams						=	array();
			$newParams['cbsubs']			=	array();

			$paramsMap						=	array( 'cbsubs30_plans' => 'plans', 'cbsubs30_mode' => 'mode' );

			for ( $i = 0, $n = $cbsubsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$cbsubs						=	array();

				foreach ( $paramsMap as $old => $new ) {
					$cbsubs[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['cbsubs'][]		=	$cbsubs;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function code( &$trigger, &$params )
	{
		$codeCount						=	substr_count( $trigger->get( 'params' ), 'code_method' );

		if ( $codeCount ) {
			$newParams					=	array();
			$newParams['code']			=	array();

			$paramsMap					=	array(	'code_method' => 'method', 'code_code' => 'code', 'code_pluginurls' => 'pluginurls',
													'code_plugins' => 'plugins', 'code_url' => 'url', 'code_return' => 'return'
												);

			for ( $i = 0, $n = $codeCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$code					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$code[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['code'][]	=	$code;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function connection( &$trigger, &$params )
	{
		$connectionCount					=	substr_count( $trigger->get( 'params' ), 'connection_users' );

		if ( $connectionCount ) {
			$newParams						=	array();
			$newParams['connection']		=	array();

			$paramsMap						=	array( 'connection_users' => 'users', 'connection_message' => 'message', 'connection_direction' => 'direction' );

			for ( $i = 0, $n = $connectionCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$connection					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$connection[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['connection'][]	=	$connection;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function content( &$trigger, &$params )
	{
		$contentCount					=	substr_count( $trigger->get( 'params' ), 'content_title' );

		if ( $contentCount ) {
			$newParams					=	array();
			$newParams['content']		=	array();

			$paramsMap					=	array(	'content_mode' => 'mode', 'content_title' => 'title', 'content_alias' => 'alias',
													'content_category_j' => 'category_j', 'content_category_k' => 'category_k', 'content_introtext' => 'introtext',
													'content_fulltext' => 'fulltext', 'content_metadesc' => 'metadesc', 'content_metakey' => 'metakey',
													'content_access' => 'access', 'content_published' => 'published', 'content_featured' => 'featured',
													'content_language' => 'language', 'content_owner' => 'owner'
												);

			for ( $i = 0, $n = $contentCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$content				=	array();

				foreach ( $paramsMap as $old => $new ) {
					$content[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['content'][]	=	$content;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function email( &$trigger, &$params )
	{
		$emailCount						=	substr_count( $trigger->get( 'params' ), 'email_to' );

		if ( $emailCount ) {
			$newParams					=	array();
			$newParams['email']			=	array();

			$paramsMap					=	array(	'email_to' => 'to', 'email_subject' => 'subject', 'email_body' => 'body',
													'email_mode' => 'mode', 'email_cc' => 'cc', 'email_bcc' => 'bcc',
													'email_attachment' => 'attachment', 'email_replyto_address' => 'replyto_address', 'email_replyto_name' => 'replyto_name',
													'email_address' => 'from_address', 'email_name' => 'from_name', 'email_mailer' => 'mailer',
													'email_mailer_sendmail' => 'mailer_sendmail', 'email_mailer_smtpauth' => 'mailer_smtpauth', 'email_mailer_smtpsecure' => 'mailer_smtpsecure',
													'email_mailer_smtpport' => 'mailer_smtpport', 'email_mailer_smtpuser' => 'mailer_smtpuser', 'email_mailer_smtppass' => 'mailer_smtppass',
													'email_mailer_smtphost' => 'mailer_smtphost'
												);

			for ( $i = 0, $n = $emailCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$email					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$email[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['email'][]	=	$email;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function field( &$trigger, &$params )
	{
		$fieldsCount					=	substr_count( $trigger->get( 'params' ), 'field_id' );

		if ( $fieldsCount ) {
			$newParams					=	array();
			$newParams['field']			=	array();

			$paramsMap					=	array(	'field_id' => 'field', 'field_operator' => 'operator',
													'field_value' => 'value', 'field_translate' => 'translate'
												);

			for ( $i = 0, $n = $fieldsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$field					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$field[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['field'][]	=	$field;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function groupjive20( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'gj20_auto' ) ) {
			$trigger->set( 'type', 'groupjive' );

			$newParams									=	array();
			$newParams['groupjive']						=	array();

			$paramsMap									=	array(	'gj20_auto' => 'mode', 'gj20_groups' => 'groups', 'gj20_grp_parent' => 'group_parent',
																	'gj20_category' => 'category', 'gj20_cat_parent' => array( 'parent', 'category_parent' ),
																	'gj20_status' => array( 'status', 'group_status' ), 'gj20_cat_unique' => 'category_unique',
																	'gj20_types' => array( 'types', 'category_types' ), 'gj20_grp_autojoin' => 'autojoin',
																	'gj20_type' => 'type', 'gj20_cat_description' => '', 'gj20_cat_owner' => '',
																	'gj20_cat_name' => '', 'gj20_grp_name' => '', 'gj20_grp_description' => '',
																	'gj20_grp_unique' => '', 'gj20_grp_owner' => ''
																);

			switch ( (int) $trigger->get( 'params' )->get( 'gj20_auto', 1, GetterInterface::INT ) ) {
				case 3:
					$paramsMap['gj20_cat_name']			=	'name';
					$paramsMap['gj20_cat_description']	=	'description';
					$paramsMap['gj20_cat_unique']		=	'unique';
					$paramsMap['gj20_cat_owner']		=	'owner';
					break;
				case 2:
					$paramsMap['gj20_cat_name']			=	'category_name';
					$paramsMap['gj20_cat_description']	=	'category_description';
					$paramsMap['gj20_grp_name']			=	'name';
					$paramsMap['gj20_grp_description']	=	'description';
					$paramsMap['gj20_grp_unique']		=	'unique';
					$paramsMap['gj20_grp_owner']		=	'owner';
					break;
			}

			$groupJive									=	array();

			foreach ( $paramsMap as $old => $new ) {
				if ( $new ) {
					if ( is_array( $new ) ) {
						foreach ( $new as $n ) {
							$groupJive[$n]				=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
						}
					} else {
						$groupJive[$new]				=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
					}
				}
			}

			$newParams['groupjive'][]					=	$groupJive;

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function k2( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'k2_mode' ) ) {
			$newParams					=	array();
			$newParams['k2']			=	array();

			$paramsMap					=	array(	'k2_mode' => 'mode', 'k2_user_group' => 'group', 'k2_gender' => 'gender',
													'k2_description' => 'description', 'k2_url' => 'url', 'k2_notes' => 'notes'
												);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['k2'][$new]	=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function kunena17( &$trigger, &$params )
	{
		$kunenaCount						=	substr_count( $trigger->get( 'params' ), 'kunena17_name' );

		if ( $kunenaCount ) {
			$trigger->set( 'type', 'kunena' );

			$newParams						=	array();
			$newParams['kunena']			=	array();

			$paramsMap						=	array( 'kunena17_name' => 'name', 'kunena17_parent' => 'parent', 'kunena17_description' => 'description' );

			for ( $i = 0, $n = $kunenaCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$kunena						=	array( 'mode' => 'category' );

				foreach ( $paramsMap as $old => $new ) {
					$kunena[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['kunena'][]		=	$kunena;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function kunena20( &$trigger, &$params )
	{
		$kunenaCount						=	substr_count( $trigger->get( 'params' ), 'kunena20_name' );

		if ( $kunenaCount ) {
			$trigger->set( 'type', 'kunena' );

			$newParams						=	array();
			$newParams['kunena']			=	array();

			$paramsMap						=	array( 'kunena20_name' => 'name', 'kunena20_parent' => 'parent', 'kunena20_description' => 'description' );

			for ( $i = 0, $n = $kunenaCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$kunena						=	array( 'mode' => 'category' );

				foreach ( $paramsMap as $old => $new ) {
					$kunena[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['kunena'][]		=	$kunena;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function loginlogout( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'loginlogout_mode' ) ) {
			$newParams							=	array();
			$newParams['loginlogout']			=	array();

			$paramsMap							=	array(	'loginlogout_mode' => 'mode', 'loginlogout_method' => 'method', 'loginlogout_username' => 'username',
															'loginlogout_email' => 'email', 'loginlogout_redirect' => 'redirect', 'loginlogout_message' => 'message'
														);

			foreach ( $paramsMap as $old => $new ) {
				$newParams['loginlogout'][$new]	=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function menu( &$trigger, &$params )
	{
		$menuCount						=	substr_count( $trigger->get( 'params' ), 'menu_title' );

		if ( $menuCount ) {
			$newParams					=	array();
			$newParams['menu']			=	array();

			$paramsMap					=	array(	'menu_title' => 'title', 'menu_type' => 'type', 'menu_class' => 'class',
													'menu_position' => 'position', 'menu_url' => 'url', 'menu_target' => 'target',
													'menu_tooltip' => 'tooltip', 'menu_img' => 'image'
												);

			for ( $i = 0, $n = $menuCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$menu					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$menu[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['menu'][]	=	$menu;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function pms( &$trigger, &$params )
	{
		$pmsCount						=	substr_count( $trigger->get( 'params' ), 'pms_from' );

		if ( $pmsCount ) {
			$newParams					=	array();
			$newParams['pms']			=	array();

			$paramsMap					=	array(	'pms_from' => 'from', 'pms_to' => 'to',
													'pms_subject' => 'subject', 'pms_message' => 'body'
												);

			for ( $i = 0, $n = $pmsCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$pms					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$pms[$new]			=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['pms'][]		=	$pms;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function privacy( &$trigger, &$params )
	{
		$privacyCount					=	substr_count( $trigger->get( 'params' ), 'privacy_user' );

		if ( $privacyCount ) {
			$newParams					=	array();
			$newParams['privacy']		=	array();

			$paramsMap					=	array(	'privacy_user' => 'owner', 'privacy_type' => 'type', 'privacy_subtype' => 'subtype',
													'privacy_item' => 'item', 'privacy_rule' => 'rule'
												);

			for ( $i = 0, $n = $privacyCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i					=	null;
				}

				$privacy				=	array();

				foreach ( $paramsMap as $old => $new ) {
					$privacy[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['privacy'][]	=	$privacy;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function query( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'query_sql' ) ) {
			$newParams					=	array();
			$newParams['query']			=	array();

			$paramsMap					=	array(	'query_sql' => 'sql', 'query_mode' => 'mode', 'query_host' => 'host',
													'query_username' => 'username', 'query_password' => 'password', 'query_database' => 'database',
													'query_charset' => 'charset', 'query_prefix' => 'prefix'
												);

			$query						=	array();

			foreach ( $paramsMap as $old => $new ) {
				$query[$new]			=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
			}

			$newParams['query'][]		=	$query;

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function redirect( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'redirect_url' ) ) {
			$newParams							=	array();
			$newParams['redirect']				=	array();

			$paramsMap							=	array( 'redirect_url' => 'url', 'redirect_message' => 'message', 'redirect_type' => 'type' );

			foreach ( $paramsMap as $old => $new ) {
				$newParams['redirect'][$new]	=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function registration( &$trigger, &$params )
	{
		if ( $trigger->get( 'params' )->has( 'registration_username' ) ) {
			$newParams									=	array();
			$newParams['registration']					=	array();

			$paramsMap									=	array(	'registration_approve' => 'approve', 'registration_confirm' => 'confirm', 'registration_usergroup' => 'usergroup',
																	'registration_username' => 'username', 'registration_password' => 'password', 'registration_email' => 'email',
																	'registration_firstname' => 'firstname', 'registration_middlename' => 'middlename', 'registration_lastname' => 'lastname',
																	'registration_supress' => 'supress', 'registration_fields' => ''
																);

			$fields										=	$trigger->get( 'params' )->get( 'registration_fields', null, GetterInterface::RAW );
			$newFields									=	array();

			if ( $fields ) {
				$fields									=	explode( "\n", $fields );

				foreach ( $fields as $pair ) {
					$field								=	explode( '=', trim( $pair ), 2 );

					if ( count( $field ) == 2 ) {
						$newFields[]					=	array( 'field' => trim( $field[0] ), 'value' => trim( $field[1] ), 'translate' => '1' );
					}
				}
			}

			$newParams['registration']['fields']		=	$newFields;

			foreach ( $paramsMap as $old => $new ) {
				if ( $new ) {
					$newParams['registration'][$new]	=	$trigger->get( 'params' )->get( $old, null, GetterInterface::RAW );
				}
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function request( &$trigger, &$params )
	{
		$requestCount						=	substr_count( $trigger->get( 'params' ), 'request_url' );

		if ( $requestCount ) {
			$newParams						=	array();
			$newParams['request']			=	array();

			$paramsMap						=	array(	'request_url' => 'url', 'request_method' => 'method', 'request_request' => '',
														'request_return' => '', 'request_error' => '', 'request_debug' => ''
													);

			for ( $i = 0, $n = $requestCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$request					=	array();

				$data						=	$trigger->get( 'params' )->get( "request_request$i", null, GetterInterface::RAW );
				$newData					=	array();

				if ( $data ) {
					$data					=	explode( "\n", $data );

					foreach ( $data as $pair ) {
						$dataPair			=	explode( '=', trim( $pair ), 2 );

						if ( count( $dataPair ) == 2 ) {
							$newData[]		=	array( 'key' => trim( $dataPair[0] ), 'value' => trim( $dataPair[1] ), 'translate' => '1' );
						}
					}
				}

				$request['request']			=	$newData;

				foreach ( $paramsMap as $old => $new ) {
					if ( $new ) {
						$request[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
					}
				}

				$newParams['request'][]		=	$request;
			}

			$params->load( $newParams );
		}
	}

	/**
	 * @param Table $trigger
	 * @param Registry $params
	 */
	public static function usergroup( &$trigger, &$params )
	{
		$usergroupCount						=	substr_count( $trigger->get( 'params' ), 'usergroup_mode' );

		if ( $usergroupCount ) {
			$newParams						=	array();
			$newParams['usergroup']			=	array();

			$paramsMap						=	array(	'usergroup_mode' => 'mode', 'usergroup_parent' => 'parent', 'usergroup_title' => 'title',
														'usergroup_add' => 'add', 'usergroup_groups' => 'groups'
													);

			for ( $i = 0, $n = $usergroupCount; $i < $n; $i++ ) {
				if ( $i == 0 ) {
					$i						=	null;
				}

				$usergroup					=	array();

				foreach ( $paramsMap as $old => $new ) {
					$usergroup[$new]		=	$trigger->get( 'params' )->get( $old . $i, null, GetterInterface::RAW );
				}

				$newParams['usergroup'][]	=	$usergroup;
			}

			$params->load( $newParams );
		}
	}
}