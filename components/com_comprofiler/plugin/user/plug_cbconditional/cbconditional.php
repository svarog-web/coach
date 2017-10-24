<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\ListTable;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onBeforeDisplayUsersList', 'getList', 'cbconditionalPlugin' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'fieldDisplay', 'cbconditionalPlugin' );
$_PLUGINS->registerFunction( 'onAfterEditATab', 'tabEdit', 'cbconditionalPlugin' );
$_PLUGINS->registerFunction( 'onAfterTabsFetch', 'tabsFetch', 'cbconditionalPlugin' );
$_PLUGINS->registerFunction( 'onAfterFieldsFetch', 'fieldsFetch', 'cbconditionalPlugin' );

class cbconditionalPlugin extends cbPluginHandler
{

	/**
	 * @param string     $value
	 * @param string|int $operator
	 * @param string     $input
	 * @return bool
	 */
	private function getMatch( $value, $operator, $input )
	{
		if ( is_array( $value ) ) {
			$value		=	implode( '|*|', $value );
		}

		$value			=	trim( $value );
		$input			=	trim( $input );

		switch ( $operator ) {
			case 1:
				$match	=	( $value != $input );
				break;
			case 2:
				$match	=	( $value > $input );
				break;
			case 3:
				$match	=	( $value < $input );
				break;
			case 4:
				$match	=	( $value >= $input );
				break;
			case 5:
				$match	=	( $value <= $input );
				break;
			case 6:
				$match	=	( ! $value );
				break;
			case 7:
				$match	=	( $value );
				break;
			case 8:
				$match	=	( stristr( $value, $input ) );
				break;
			case 9:
				$match	=	( ! stristr( $value, $input ) );
				break;
			case 10:
				$match	=	( preg_match( $input, $value ) );
				break;
			case 11:
				$match	=	( ! preg_match( $input, $value ) );
				break;
			case 0:
			default:
				$match	=	( $value == $input );
				break;
		}

		return (bool) $match;
	}

	/**
	 * @param string $param
	 * @return array
	 */
	private function getFieldsArray( $param )
	{
		if ( $param ) {
			$param	=	explode( '|*|', $param );

			cbArrayToInts( $param );

			$param	=	array_values( array_unique( $param ) );
		}

		if ( ! is_array( $param ) ) {
			$param	=	array();
		}

		return $param;
	}

	/**
	 * @param null|string|int|TabTable[] $tabs
	 * @param string                     $reason
	 * @param int                        $userId
	 * @param bool                       $jquery
	 * @param string                     $formatting
	 * @param bool                       $tabbed
	 * @return stdClass
	 */
	private function getTabConditional( $tabs, $reason, $userId, $jquery = false, $formatting = 'table', $tabbed = true )
	{
		global $_CB_database, $_CB_framework;

		$reset														=	$this->params->get( 'cond_reset', true, GetterInterface::BOOLEAN );
		$debug														=	$this->params->get( 'cond_debug', false, GetterInterface::BOOLEAN );

		$condition													=	new stdClass();
		$condition->hide											=	array();
		$condition->hideStatic										=	array();

		static $userCache											=	array();

		if ( ! isset( $userCache[$userId] ) ) {
			$cbUser													=	CBuser::getInstance( (int) $userId, false );
			$cmsUser												=	Application::User( (int) $userId );

			$userCache[$userId]										=	array( $cbUser, $cbUser->getUserData(), $cmsUser->getAuthorisedViewLevels(), $cmsUser->getAuthorisedGroups() );
		}

		/** @var CBuser $cbUser */
		$cbUser														=	$userCache[$userId][0];
		/** @var UserTable $user */
		$user														=	$userCache[$userId][1];
		/** @var array $userAccessLevels */
		$userAccessLevels											=	$userCache[$userId][2];
		/** @var array $userUsergroups */
		$userUsergroups												=	$userCache[$userId][3];

		/** @var TabTable[] $tabCache */
		static $tabCache											=	array();

		if ( ! $tabs ) {
			/** @var TabTable[] $tabsCache */
			static $tabsCache										=	array();

			if ( ! isset( $tabsCache[$user->id] ) ) {
				$cbTabs												=	$cbUser->_getCbTabs();
				$tabsCache[$user->id]								=	$cbTabs->_getTabsDb( $user, 'adminfulllist' );
			}

			$tabs													=	$tabsCache[$user->id];
		} elseif ( ! is_array( $tabs ) ) {
			if ( is_string( $tabs ) || is_integer( $tabs ) ) {
				$tabId												=	(int) $tabs;

				if ( $tabId ) {
					if ( ! isset( $tabCache[$tabId] ) ) {
						$tab										=	new TabTable();

						$tab->load( $tabId );

						$tabCache[$tabId]							=	$tab;
					}

					$tabs											=	$tabCache[$tabId];
				}
			}

			$tabs													=	array( $tabs );
		} elseif ( is_array( $tabs ) ) {
			$tabArray												=	array();

			foreach ( $tabs as $tabId ) {
				if ( is_string( $tabId ) || is_integer( $tabId ) ) {
					$tabId											=	(int) $tabId;

					if ( $tabId ) {
						if ( ! isset( $tabCache[$tabId] ) ) {
							$tab									=	new TabTable();

							$tab->load( $tabId );

							$tabCache[$tabId]						=	$tab;
						}

						$tabArray[]									=	$tabCache[$tabId];
					}
				} elseif ( $tabId instanceof TabTable ) {
					$tabArray[]										=	$tabId;
				}
			}

			$tabs													=	$tabArray;
		}

		/** @var Registry[] $tabParams */
		static $tabParams											=	array();
		/** @var FieldTable[] $fields */
		static $fields												=	array();
		/** @var FieldTable[] $tabFields */
		static $tabFields											=	array();
		/** @var array[] $conditioned */
		static $conditioned											=	array();

		$uId														=	$user->get( 'id', 0, GetterInterface::INT );

		if ( $tabs ) foreach ( $tabs as $tab ) {
			if ( $tab instanceof TabTable ) {
				$tId												=	$tab->get( 'tabid', 0, GetterInterface::INT );

				if ( ! isset( $conditioned[$tId][$uId][$reason][$jquery] ) ) {
					$tabConditions									=	array();

					$conditioned[$tId][$uId][$reason][$jquery]		=	$tabConditions;

					if ( ! isset( $tabParams[$tId] ) ) {
						if ( ! ( $tab->params instanceof ParamsInterface ) ) {
							$tab->params							=	new Registry( $tab->params );
						}

						$tabParams[$tId]							=	$tab->params;
					}

					$params											=	$tabParams[$tId];

					for ( $i = 1; $i <= 5; $i++ ) {
						$conditional								=	( $i > 1 ? $i : null );
						$display									=	$params->get( 'cbconditional_display' . $conditional, false, GetterInterface::BOOLEAN );

						if ( $reason == 'profile' ) {
							if ( ! $params->get( 'cbconditional_target_view' . $conditional, true, GetterInterface::BOOLEAN ) ) {
								$display							=	false;
							}
						} elseif ( $reason == 'edit' ) {
							if ( ! $params->get( 'cbconditional_target_edit' . $conditional, false, GetterInterface::BOOLEAN ) ) {
								$display							=	false;
							}
						} elseif ( $reason == 'register' ) {
							if ( ! $params->get( 'cbconditional_target_reg' . $conditional, false, GetterInterface::BOOLEAN ) ) {
								$display							=	false;
							}
						}

						if ( $display ) {
							$fieldName								=	$params->get( 'cbconditional_field' . $conditional, null, GetterInterface::STRING );

							if ( $fieldName ) {
								$operator							=	$params->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT );
								$value								=	$cbUser->replaceUserVars( $params->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ), false, true, $this->getExtras(), $params->get( 'cbconditional_value_translate' . $conditional, false, GetterInterface::BOOLEAN ) );

								if ( in_array( $operator, array( '6', '7' ) ) ) {
									$value							=	null;
								}

								$mode								=	$params->get( 'cbconditional_mode' . $conditional, 0, GetterInterface::INT );
								$static								=	false;

								switch ( $fieldName ) {
									case 'customvalue':
										$fieldValue					=	$cbUser->replaceUserVars( $params->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ), false, true, $this->getExtras(), $params->get( 'cbconditional_customvalue_translate' . $conditional, false, GetterInterface::BOOLEAN ) );
										$static						=	true;
										break;
									case 'customviewaccesslevels':
										$accessLevels				=	cbToArrayOfInt( explode( '|*|', $params->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ) ) );
										$fieldValue					=	0;

										foreach ( $accessLevels as $accessLevel ) {
											if ( in_array( $accessLevel, $userAccessLevels ) ) {
												$fieldValue			=	1;
												break;
											}
										}

										$operator					=	0;
										$value						=	1;
										$static						=	true;
										break;
									case 'customusergroups':
										$userGroups					=	cbToArrayOfInt( explode( '|*|', $params->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ) ) );
										$fieldValue					=	0;

										foreach ( $userGroups as $userGroup ) {
											if ( in_array( $userGroup, $userUsergroups ) ) {
												$fieldValue			=	1;
												break;
											}
										}

										$operator					=	0;
										$value						=	1;
										$static						=	true;
										break;
									default:
										if ( ! isset( $fields[$fieldName] ) ) {
											$field					=	new FieldTable();

											$field->load( array( 'name' => $fieldName ) );

											$fields[$fieldName]		=	$field;
										}

										if ( ! $fields[$fieldName]->get( 'fieldid', 0, GetterInterface::INT ) ) {
											continue 2;
										}

										$fieldValue					=	$this->getFieldValue( $user, $fields[$fieldName], $reason );
										break;
								}

								if ( $jquery ) {
									$_CB_framework->addJQueryPlugin( 'cbcondition', '/components/com_comprofiler/plugin/user/plug_cbconditional/js/cbcondition.js' );

									$js								=	"var tabCondition = ['#cbtp_$tId'];";

									if ( $tabbed ) {
										$js							.=	"tabCondition.push( '#cbtabpane$tId' );";
									} else {
										if ( in_array( $formatting, array( 'tables', 'divs' ) ) ) {
											$js						.=	"tabCondition.push( '#cbtf_$tId' );";
										} else {
											if ( ! isset( $tabFields[$tId] ) ) {
												$query				=	'SELECT *'
																	.	"\n FROM " .  $_CB_database->NameQuote( '#__comprofiler_fields' )
																	.	"\n WHERE " . $_CB_database->NameQuote( 'tabid' ) . " = " . (int) $tId;
												$_CB_database->setQuery( $query );
												$tabFields[$tId]	=	$_CB_database->loadObjectList( null, '\CB\Database\Table\FieldTable', array( $_CB_database ) );
											}

											foreach ( $tabFields[$tId] as $tabField ) {
												/** @var  FieldTable $tabField */
												$fId				=	$tabField->get( 'fieldid', 0, GetterInterface::INT );

												$js					.=	"tabCondition.push( '#cbfr_$fId,#cbfr_' . $fId . '__verify,#cbfrd_$fId,#cbfrd_' . $fId . '__verify' );";
											}
										}
									}

									switch ( $fieldName ) {
										case 'customvalue':
										case 'customviewaccesslevels':
										case 'customusergroups':
											$js						.=	"$.cbcondition({"
																	.		"conditions: [{"
																	.			"operator: " . (int) $operator . ","
																	.			"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( '|*|', $fieldValue ) : $fieldValue ) ) ) . "',"
																	.			"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( '|*|', $value ) : $value ) ) ) . "',"
																	.			( $mode ? "show: tabCondition," : "hide: tabCondition," )
																	.			"reset: " . (int) $reset . ""
																	.		"}],"
																	.		"debug: " . (int) $debug . ""
																	.	"});";
											break;
										default:
											$fieldId				=	$fields[$fieldName]->get( 'fieldid', 0, GetterInterface::INT );

											$js						.=	"$( '#cbfr_" . (int) $fieldId . ",#cbfrd_" . (int) $fieldId . "' ).cbcondition({"
																	.		"conditions: [{"
																	.			"operator: " . (int) $operator . ","
																	.			"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( '|*|', $fieldValue ) : $fieldValue ) ) ) . "',"
																	.			"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( '|*|', $value ) : $value ) ) ) . "',"
																	.			( $mode ? "show: tabCondition," : "hide: tabCondition," )
																	.			"reset: " . (int) $reset . ""
																	.		"}],"
																	.		"debug: " . (int) $debug . ""
																	.	"});";
											break;
									}

									$_CB_framework->outputCbJQuery( $js, 'cbcondition' );
								}

								$tabCondition						=	array(	'match'		=>	$this->getMatch( $fieldValue, $operator, $value ),
																				'mode'		=>	$mode,
																				'tab'		=>	$tId,
																				'static'	=>	$static
																			);

								$tabConditions[]					=	$tabCondition;

								if ( $debug ) {
									var_dump( json_encode( array( 'location' => $reason, 'input' => $fieldValue, 'operator' => $operator, 'value' => $value, 'condition' => $tabCondition ) ) );
								}
							}
						}
					}

					$conditioned[$tId][$uId][$reason][$jquery]		=	$tabConditions;
				}

				$conditions											=	$conditioned[$tId][$uId][$reason][$jquery];

				foreach ( $conditions as $cond ) {
					if ( $cond['match'] ) {
						if ( ( ! $cond['mode'] ) && ( ! in_array( $cond['tab'], $condition->hide ) ) ) {
							$condition->hide[]						=	$cond['tab'];

							if ( $cond['static'] ) {
								$condition->hideStatic[]			=	$cond['tab'];
							}
						}
					} else {
						if ( $cond['mode'] && ( ! in_array( $cond['tab'], $condition->hide ) ) ) {
							$condition->hide[]						=	$cond['tab'];

							if ( $cond['static'] ) {
								$condition->hideStatic[]			=	$cond['tab'];
							}
						}
					}
				}
			}
		}

		return $condition;
	}

	/**
	 * @param null|string|int|FieldTable[] $fields
	 * @param string                       $reason
	 * @param int                          $userId
	 * @param bool                         $jquery
	 * @return stdClass
	 */
	private function getFieldConditional( $fields, $reason, $userId, $jquery = false )
	{
		global $_CB_framework;

		$reset														=	$this->params->get( 'cond_reset', true, GetterInterface::BOOLEAN );
		$debug														=	$this->params->get( 'cond_debug', false, GetterInterface::BOOLEAN );

		$condition													=	new stdClass();
		$condition->show											=	array();
		$condition->hide											=	array();
		$condition->showStatic										=	array();
		$condition->hideStatic										=	array();

		static $userCache											=	array();

		if ( ! isset( $userCache[$userId] ) ) {
			$cbUser													=	CBuser::getInstance( (int) $userId, false );
			$cmsUser												=	Application::User( (int) $userId );

			$userCache[$userId]										=	array( $cbUser, $cbUser->getUserData(), $cmsUser->getAuthorisedViewLevels(), $cmsUser->getAuthorisedGroups() );
		}

		/** @var CBuser $cbUser */
		$cbUser														=	$userCache[$userId][0];
		/** @var UserTable $user */
		$user														=	$userCache[$userId][1];
		/** @var array $userAccessLevels */
		$userAccessLevels											=	$userCache[$userId][2];
		/** @var array $userUsergroups */
		$userUsergroups												=	$userCache[$userId][3];

		/** @var FieldTable[] $fieldCache */
		static $fieldCache											=	array();

		if ( ! $fields ) {
			/** @var FieldTable[] $tabsCache */
			static $tabsCache										=	array();

			if ( ! isset( $tabsCache[$user->id] ) ) {
				$cbTabs												=	$cbUser->_getCbTabs();
				$tabsCache[$user->id]								=	$cbTabs->_getTabFieldsDb( null, $user, 'adminfulllist', null, true, true );
			}

			$fields													=	$tabsCache[$user->id];
		} elseif ( ! is_array( $fields ) ) {
			if ( is_string( $fields ) || is_integer( $fields ) ) {
				$fieldId											=	(int) $fields;

				if ( $fieldId ) {
					if ( ! isset( $fieldCache[$fieldId] ) ) {
						$field										=	new FieldTable();

						$field->load( $fieldId );

						$fieldCache[$fieldId]						=	$field;
					}

					$fields											=	$fieldCache[$fieldId];
				}
			}

			$fields													=	array( $fields );
		} elseif ( is_array( $fields ) ) {
			$fieldArray												=	array();

			foreach ( $fields as $fieldId ) {
				if ( is_string( $fieldId ) || is_integer( $fieldId ) ) {
					$fieldId										=	(int) $fieldId;

					if ( $fieldId ) {
						if ( ! isset( $fieldCache[$fieldId] ) ) {
							$field									=	new FieldTable();

							$field->load( $fieldId );

							$fieldCache[$fieldId]					=	$field;
						}

						$fieldArray[]								=	$fieldCache[$fieldId];
					}
				} elseif ( $fieldId instanceof FieldTable ) {
					$fieldArray[]									=	$fieldId;
				}
			}

			$fields													=	$fieldArray;
		}

		/** @var Registry[] $fieldParams */
		static $fieldParams											=	array();
		/** @var array[] $conditioned */
		static $conditioned											=	array();

		$uId														=	$user->get( 'id', 0, GetterInterface::INT );

		if ( $fields ) foreach ( $fields as $field ) {
			if ( $field instanceof FieldTable ) {
				$fId												=	$field->get( 'fieldid', 0, GetterInterface::INT );

				if ( ! isset( $conditioned[$fId][$uId][$reason][$jquery] ) ) {
					$fieldConditions								=	array();

					$conditioned[$fId][$uId][$reason][$jquery]		=	$fieldConditions;

					if ( ! isset( $fieldParams[$fId] ) ) {
						if ( ! ( $field->params instanceof ParamsInterface ) ) {
							$field->params							=	new Registry( $field->params );
						}

						$fieldParams[$fId]							=	$field->params;
					}

					$params											=	$fieldParams[$fId];

					if ( ! isset( $fieldCache[$fId] ) ) {
						$fieldCache[$fId]							=	$field;
					}

					for ( $i = 1; $i <= 5; $i++ ) {
						$conditional								=	( $i > 1 ? $i : null );
						$display									=	$params->get( 'cbconditional_display' . $conditional, 0, GetterInterface::INT );

						if ( $reason == 'register' ) {
							if ( ! $params->get( 'cbconditional_target_reg' . $conditional, true, GetterInterface::BOOLEAN ) ) {
								$display							=	0;
							}
						} elseif ( $reason == 'edit' ) {
							if ( ! $params->get( 'cbconditional_target_edit' . $conditional, true, GetterInterface::BOOLEAN ) ) {
								$display							=	0;
							}
						} elseif ( $reason == 'profile' ) {
							if ( ! $params->get( 'cbconditional_target_view' . $conditional, true, GetterInterface::BOOLEAN ) ) {
								$display							=	0;
							}
						} elseif ( $reason == 'search' ) {
							if ( ! $params->get( 'cbconditional_target_search' . $conditional, false, GetterInterface::BOOLEAN ) ) {
								$display							=	0;
							}
						} elseif ( $reason == 'list' ) {
							if ( ! $params->get( 'cbconditional_target_list' . $conditional, true, GetterInterface::BOOLEAN ) ) {
								$display							=	0;
							}
						}

						if ( $display ) {
							if ( $display == 2 ) {
								$mode								=	$params->get( 'cbconditional_mode' . $conditional, 0, GetterInterface::INT );
								$show								=	$this->getFieldsArray( ( $mode == 1 ? $fId : null ) );
								$hide								=	$this->getFieldsArray( ( $mode == 0 ? $fId : null ) );
								$optshow							=	array();
								$opthide							=	array();

								$fieldPair							=	explode( ',', $params->get( 'cbconditional_field' . $conditional, null, GetterInterface::STRING ) );

								if ( count( $fieldPair ) < 2 ) {
									array_unshift( $fieldPair, 0 );
								}

								$fieldId							=	(int) array_shift( $fieldPair );
								$fieldName							=	array_pop( $fieldPair );

								if ( ! isset( $fieldCache[$fieldId] ) ) {
									$fld							=	new FieldTable();

									$fld->load( $fieldId );

									$fieldCache[$fieldId]			=	$fld;
								}

								$fieldObj							=	$fieldCache[$fieldId];
							} else {
								$show								=	$this->getFieldsArray( $params->get( 'cbconditional_show' . $conditional, null, GetterInterface::STRING ) );
								$hide								=	$this->getFieldsArray( $params->get( 'cbconditional_hide' . $conditional, null, GetterInterface::STRING ) );
								$optshow							=	$this->getFieldsArray( $params->get( 'cbconditional_options_show' . $conditional, null, GetterInterface::STRING ) );
								$opthide							=	$this->getFieldsArray( $params->get( 'cbconditional_options_hide' . $conditional, null, GetterInterface::STRING ) );

								$fieldId							=	$fId;
								$fieldName							=	$field->get( 'name', null, GetterInterface::STRING );
								$fieldObj							=	$field;
							}

							if ( $show || $hide || $optshow || $opthide ) {
								$operator							=	$params->get( 'cbconditional_operator' . $conditional, 0, GetterInterface::INT );
								$value								=	$cbUser->replaceUserVars( $params->get( 'cbconditional_value' . $conditional, null, GetterInterface::RAW ), false, true, $this->getExtras(), $params->get( 'cbconditional_value_translate' . $conditional, false, GetterInterface::BOOLEAN ) );

								if ( in_array( $operator, array( 6, 7 ) ) ) {
									$value							=	null;
								}

								$static								=	false;

								switch ( $fieldName ) {
									case 'customvalue':
										$fieldValue					=	$cbUser->replaceUserVars( $params->get( 'cbconditional_customvalue' . $conditional, null, GetterInterface::RAW ), false, true, $this->getExtras(), $params->get( 'cbconditional_customvalue_translate' . $conditional, false, GetterInterface::BOOLEAN ) );
										$static						=	true;
										break;
									case 'customviewaccesslevels':
										$accessLevels				=	cbToArrayOfInt( explode( '|*|', $params->get( 'cbconditional_customviewaccesslevels' . $conditional, null, GetterInterface::STRING ) ) );
										$fieldValue					=	0;

										foreach ( $accessLevels as $accessLevel ) {
											if ( in_array( $accessLevel, $userAccessLevels ) ) {
												$fieldValue			=	1;
												break;
											}
										}

										$operator					=	0;
										$value						=	1;
										$static						=	true;
										break;
									case 'customusergroups':
										$userGroups					=	cbToArrayOfInt( explode( '|*|', $params->get( 'cbconditional_customusergroups' . $conditional, null, GetterInterface::STRING ) ) );
										$fieldValue					=	0;

										foreach ( $userGroups as $userGroup ) {
											if ( in_array( $userGroup, $userUsergroups ) ) {
												$fieldValue			=	1;
												break;
											}
										}

										$operator					=	0;
										$value						=	1;
										$static						=	true;
										break;
									default:
										if ( ! $fieldObj->get( 'fieldid', 0, GetterInterface::INT ) ) {
											continue 2;
										}

										$fieldValue					=	$this->getFieldValue( $user, $fieldObj, $reason );
										break;
								}

								if ( $jquery ) {
									$_CB_framework->addJQueryPlugin( 'cbcondition', '/components/com_comprofiler/plugin/user/plug_cbconditional/js/cbcondition.js' );

									$js								=	"var conditionShow = [];"
																	.	"var conditionHide = [];";

									foreach ( $show as $v ) {
										$js							.=	"conditionShow.push( '#cbfr_$v,#cbfr_" . $v . "__verify,#cbfrd_$v,#cbfrd_" . $v . "__verify' );";
									}

									foreach ( $hide as $k => $v ) {
										$js							.=	"conditionHide.push( '#cbfr_$v,#cbfr_" . $v . "__verify,#cbfrd_$v,#cbfrd_" . $v . "__verify' );";
									}

									foreach ( $optshow as $k => $v ) {
										$js							.=	"conditionShow.push( '#cbf$v' );";
									}

									foreach ( $opthide as $k => $v ) {
										$js							.=	"conditionHide.push( '#cbf$v' );";
									}

									switch ( $fieldName ) {
										case 'customvalue':
										case 'customviewaccesslevels':
										case 'customusergroups':
											$js						.=	"$.cbcondition({"
																	.		"conditions: [{"
																	.			"operator: " . (int) $operator . ","
																	.			"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( '|*|', $fieldValue ) : $fieldValue ) ) ) . "',"
																	.			"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( '|*|', $value ) : $value ) ) ) . "',"
																	.			"show: conditionShow,"
																	.			"hide: conditionHide,"
																	.			"reset: " . (int) $reset . ""
																	.		"}],"
																	.		"debug: " . (int) $debug . ""
																	.	"});";
											break;
										default:
											$js						.=	"$( '#cbfr_" . (int) $fieldId . ",#cbfrd_" . (int) $fieldId . "' ).cbcondition({"
																	.		"conditions: [{"
																	.			"operator: " . (int) $operator . ","
																	.			"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( '|*|', $fieldValue ) : $fieldValue ) ) ) . "',"
																	.			"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( '|*|', $value ) : $value ) ) ) . "',"
																	.			"show: conditionShow,"
																	.			"hide: conditionHide,"
																	.			"reset: " . (int) $reset . ""
																	.		"}],"
																	.		"debug: " . (int) $debug . ""
																	.	"});";
											break;
									}

									$_CB_framework->outputCbJQuery( $js, 'cbcondition' );
								}

								$fieldCondition						=	array(	'match'		=>	$this->getMatch( $fieldValue, $operator, $value ),
																				'show'		=>	$show,
																				'hide'		=>	$hide,
																				'static'	=>	$static
																			);

								$fieldConditions[]					=	$fieldCondition;

								if ( $debug ) {
									var_dump( json_encode( array( 'location' => $reason, 'input' => $fieldValue, 'operator' => $operator, 'value' => $value, 'condition' => $fieldCondition ) ) );
								}
							}
						}
					}

					$conditioned[$fId][$uId][$reason][$jquery]		=	$fieldConditions;
				}

				$conditions											=	$conditioned[$fId][$uId][$reason][$jquery];

				foreach ( $conditions as $cond ) {
					if ( $cond['match'] ) {
						foreach ( $cond['show'] as $v ) {
							$v										=	(int) $v;

							if ( in_array( $v, $condition->hide ) ) {
								$k									=	array_search( $v, $condition->show );

								unset( $condition->hide[$k] );
							}

							if ( ! in_array( $v, $condition->show ) ) {
								$condition->show[]					=	$v;

								if ( $cond['static'] ) {
									$condition->showStatic[]		=	$v;
								}
							}
						}

						foreach ( $cond['hide'] as $v ) {
							$v										=	(int) $v;

							if ( in_array( $v, $condition->show ) ) {
								$k									=	array_search( $v, $condition->show );

								unset( $condition->show[$k] );
							}

							if ( ! in_array( $v, $condition->hide ) ) {
								$condition->hide[]					=	$v;

								if ( $cond['static'] ) {
									$condition->hideStatic[]		=	$v;
								}
							}
						}
					} else {
						foreach ( $cond['show'] as $v ) {
							$v										=	(int) $v;

							if ( in_array( $v, $condition->show ) ) {
								$k									=	array_search( $v, $condition->show );

								unset( $condition->show[$k] );
							}

							if ( ! in_array( $v, $condition->hide ) ) {
								$condition->hide[]					=	$v;

								if ( $cond['static'] ) {
									$condition->hideStatic[]		=	$v;
								}
							}
						}

						foreach ( $cond['hide'] as $v ) {
							$v										=	(int) $v;

							if ( in_array( $v, $condition->hide ) ) {
								$k									=	array_search( $v, $condition->show );

								unset( $condition->hide[$k] );
							}

							if ( ! in_array( $v, $condition->show ) ) {
								$condition->show[]					=	$v;

								if ( $cond['static'] ) {
									$condition->showStatic[]		=	$v;
								}
							}
						}
					}
				}
			}
		}

		return $condition;
	}

	/**
	 * @param UserTable  $user
	 * @param FieldTable $field
	 * @param string     $reason
	 * @return array|mixed|string
	 */
	private function getFieldValue( $user, $field, $reason )
	{
		global $_PLUGINS;

		static $values											=	array();

		$fieldId												=	$field->get( 'fieldid', 0, GetterInterface::INT );

		if ( ! $fieldId ) {
			return null;
		}

		$reset													=	$this->params->get( 'cond_reset', true, GetterInterface::BOOLEAN );
		$post													=	$this->getInput()->getNamespaceRegistry( 'post' );
		$checkPost												=	false;

		if ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() ) {
			$view												=	$this->input( 'view', null, GetterInterface::STRING );

			if ( Application::Cms()->getClientId() && in_array( $view, array( 'apply', 'save' ) ) ) {
				$checkPost										=	true;
			} elseif ( in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) ) {
				$checkPost										=	true;
			}

			if ( $checkPost ) {
				if ( $field->get( 'readonly', 0, GetterInterface::INT ) && ( $reason != 'register' ) && ( ! Application::Cms()->getClientId() ) ) {
					$checkPost									=	false;
				} elseif ( ( $reason == 'register' ) && ( ! $field->get( 'registration', 1, GetterInterface::INT ) ) ) {
					$checkPost									=	false;
				} elseif ( ( $reason == 'edit' ) && ( ! $field->get( 'edit', 1, GetterInterface::INT ) ) ) {
					$checkPost									=	false;
				}
			}
		}

		$userId													=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $values[$fieldId][$userId][$reason][$checkPost] ) ) {
			if ( ! ( $field->params instanceof ParamsInterface ) ) {
				$field->params									=	new Registry( $field->params );
			}

			$fieldName											=	$field->get( 'name', null, GetterInterface::STRING );
			$fieldValue											=	null;

			if ( $checkPost ) {
				$postUser										=	new UserTable();

				foreach ( array_keys( get_object_vars( $user ) ) as $k ) {
					if ( substr( $k, 0, 1 ) != '_' ) {
						$postUser->set( $k, $user->get( $k ) );
					}
				}

				if ( ! $post->has( $fieldName ) ) {
					if ( $reset ) {
						switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
							case 'date':
								$post->set( $fieldName, '0000-00-00' );
								break;
							case 'datetime':
								$post->set( $fieldName, '0000-00-00 00:00:00' );
								break;
							case 'integer':
							case 'points':
							case 'rating':
							case 'checkbox':
							case 'terms':
							case 'counter':
								$post->set( $fieldName, 0 );
								break;
							case 'image':
								$post->set( $fieldName, '' );
								$post->set( $fieldName . 'approved', 0 );
								break;
							default:
								foreach ( $field->getTableColumns() as $column ) {
									$post->set( $column, '' );
								}
								break;
						}
					} else {
						$post->set( $fieldName, null );
					}
				}

				$postUser->bindThisUserFromDbArray( $post->asArray() );

				$fieldValue										=	$postUser->get( $fieldName );

				if ( is_array( $fieldValue ) ) {
					$fieldValue									=	implode( '|*|', $fieldValue );
				}

				if ( $fieldValue === null ) {
					$field->set( '_noCondition', true );

					$fieldValue									=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$postUser, 'php', 'none', 'profile', 0 ), $field );

					$field->set( '_noCondition', false );

					if ( is_array( $fieldValue ) ) {
						$fieldValue								=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue							=	implode( '|*|', $fieldValue );
						}
					}
				}
			}

			if ( $fieldValue === null ) {
				$fieldValue										=	$user->get( $fieldName );

				if ( is_array( $fieldValue ) ) {
					$fieldValue									=	implode( '|*|', $fieldValue );
				}

				if ( $fieldValue === null ) {
					$field->set( '_noCondition', true );

					$fieldValue									=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'php', 'none', 'profile', 0 ), $field );

					$field->set( '_noCondition', false );

					if ( is_array( $fieldValue ) ) {
						$fieldValue								=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue							=	implode( '|*|', $fieldValue );
						}
					}
				}
			}

			$values[$fieldId][$userId][$reason][$checkPost]		=	$fieldValue;
		}

		return $values[$fieldId][$userId][$reason][$checkPost];
	}

	/**
	 * Parses substitution extras array from available variables
	 *
	 * @return array
	 */
	private function getExtras()
	{
		static $extras		=	array();

		if ( empty( $extras ) ) {
			$get			=	$this->getInput()->getNamespaceRegistry( 'get' );

			if ( $get ) {
				$this->prepareExtras( 'get', $get->asArray(), $extras );
			}

			$post			=	$this->getInput()->getNamespaceRegistry( 'post' );

			if ( $post ) {
				$this->prepareExtras( 'post', $post->asArray(), $extras );
			}

			$files			=	$this->getInput()->getNamespaceRegistry( 'files' );

			if ( $files ) {
				$this->prepareExtras( 'files', $files->asArray(), $extras );
			}

			$cookie			=	$this->getInput()->getNamespaceRegistry( 'cookie' );

			if ( $cookie ) {
				$this->prepareExtras( 'cookie', $cookie->asArray(), $extras );
			}

			$server			=	$this->getInput()->getNamespaceRegistry( 'server' );

			if ( $server ) {
				$this->prepareExtras( 'server', $server->asArray(), $extras );
			}

			$env			=	$this->getInput()->getNamespaceRegistry( 'env' );

			if ( $env ) {
				$this->prepareExtras( 'env', $env->asArray(), $extras );
			}
		}

		return $extras;
	}

	/**
	 * Converts array or object into pathed extras substitutions
	 *
	 * @param string       $prefix
	 * @param array|object $items
	 * @param array        $extras
	 */
	private function prepareExtras( $prefix, $items, &$extras )
	{
		foreach ( $items as $k => $v ) {
			if ( is_array( $v ) ) {
				$multi					=	false;

				foreach ( $v as $kv => $cv ) {
					if ( is_numeric( $kv ) ) {
						$kv				=	(int) $kv;
					}

					if ( is_object( $cv ) || is_array( $cv ) || ( $kv && ( ! is_int( $kv ) ) ) ) {
						$multi			=	true;
					}
				}

				if ( ! $multi ) {
					$v					=	implode( '|*|', $v );
				}
			}

			$k							=	'_' . ltrim( str_replace( ' ', '_', trim( strtolower( $k ) ) ), '_' );

			if ( ( ! is_object( $v ) ) && ( ! is_array( $v ) ) ) {
				$extras[$prefix . $k]	=	$v;
			} elseif ( $v ) {
				if ( is_object( $v ) ) {
					/** @var object $v */
					$subItems			=	get_object_vars( $v );
				} else {
					$subItems			=	$v;
				}

				$this->prepareExtras( $prefix . $k, $subItems, $extras );
			}
		}
	}

	/**
	 * @param ListTable    $row
	 * @param UserTable[]  $users
	 * @param array        $columns
	 * @param FieldTable[] $fields
	 * @param array        $input
	 * @param int          $listid
	 * @param string|null  $search
	 * @param int          $Itemid
	 * @param int          $ui
	 */
	public function getList( &$row, &$users, &$columns, &$fields, &$input, $listid, &$search, &$Itemid, $ui )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( $search !== null ) ) {
			$tabs								=	array();

			foreach ( $fields as $field ) {
				if ( ! in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabs ) ) {
					$tabs[]						=	$field->get( 'tabid', 0, GetterInterface::INT );
				}
			}

			if ( $users ) foreach( $users as $k => $user ) {
				if ( isset( $users[$k] ) ) {
					$hide						=	array();

					if ( $tabs ) {
						$tabCondition			=	$this->getTabConditional( $tabs, 'list', $user->get( 'id', 0, GetterInterface::INT ) );

						if ( $tabCondition ) {
							foreach ( $fields as $field ) {
								if ( in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabCondition->hide ) ) {
									$hide[]		=	$field->get( 'fieldid', 0, GetterInterface::INT );
								}
							}
						}
					}

					if ( ! $hide ) {
						$condition				=	$this->getFieldConditional( $fields, 'list', $user->get( 'id', 0, GetterInterface::INT ) );

						if ( $condition->hide ) {
							foreach ( $fields as $field ) {
								if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $condition->hide ) ) {
									$hide[]		=	$field->get( 'fieldid', 0, GetterInterface::INT );
								}
							}
						}
					}

					if ( $hide ) {
						foreach ( $fields as $field ) {
							if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $hide ) && ( $this->input( $field->get( 'name', null, GetterInterface::STRING ), null, GetterInterface::RAW ) != '' ) ) {
								unset( $users[$k] );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param string    $content
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param array     $postdata
	 * @param string    $output
	 * @param string    $formatting
	 * @param string    $reason
	 * @param bool      $tabbed
	 */
	public function tabEdit( &$content, &$tab, &$user, &$postdata, $output, $formatting, $reason, $tabbed )
	{
		if ( ( ! Application::Cms()->getClientId() ) || $this->params->get( 'cond_backend', false, GetterInterface::BOOLEAN ) ) {
			if ( ( $output == 'htmledit' ) && ( $reason != 'search' ) ) {
				$condition			=	$this->getTabConditional( $tab, $reason, $user->get( 'id', 0, GetterInterface::INT ), ( $formatting != 'none' ), $formatting, $tabbed );
				$display			=	true;

				if ( in_array( $tab->get( 'tabid', 0, GetterInterface::INT ), $condition->hideStatic ) ) {
					$display		=	false;
				} elseif ( ( $formatting == 'none' ) && in_array( $tab->get( 'tabid', 0, GetterInterface::INT ), $condition->hide ) ) {
					$display		=	false;
				}

				if ( ! $display ) {
					$content		=	'';
				}
			}
		}
	}

	/**
	 * @param TabTable[] $tabs
	 * @param UserTable  $user
	 * @param string     $reason
	 */
	public function tabsFetch( &$tabs, &$user, $reason )
	{
		$post				=	$this->getInput()->getNamespaceRegistry( 'post' );
		$view				=	$this->input( 'view', null, GetterInterface::STRING );

		if ( ! Application::Cms()->getClientId() ) {
			$checkView		=	( ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) ) || ( $reason == 'profile' ) );
		} elseif ( Application::Cms()->getClientId() && $this->params->get( 'cond_backend', false, GetterInterface::BOOLEAN ) ) {
			$checkView		=	( ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'apply', 'save' ) ) ) || ( $reason == 'profile' ) );
		} else {
			$checkView		=	false;
		}

		if ( $checkView && $tabs && ( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) ) ) {
			$condition		=	$this->getTabConditional( $tabs, $reason, $user->get( 'id', 0, GetterInterface::INT ) );

			if ( $condition ) {
				foreach ( $tabs as $k => $tab ) {
					if ( in_array( $tab->get( 'tabid', 0, GetterInterface::INT ), $condition->hide ) ) {
						unset( $tabs[$k] );
					}
				}
			}
		}
	}

	/**
	 * @param FieldTable[] $fields
	 * @param UserTable    $user
	 * @param string       $reason
	 * @param int          $tabid
	 * @param int|string   $fieldIdOrName
	 * @param bool         $fullAccess
	 */
	public function fieldsFetch( &$fields, &$user, $reason, $tabid, $fieldIdOrName, $fullAccess )
	{
		if ( $fieldIdOrName ) {
			// getFields usage provides this and in this case $user is the viewing user and not the profile owner so skip this check:
			return;
		}

		$post										=	$this->getInput()->getNamespaceRegistry( 'post' );
		$view										=	$this->input( 'view', null, GetterInterface::STRING );
		$reset										=	$this->params->get( 'cond_reset', true, GetterInterface::BOOLEAN );

		if ( ( ! Application::Cms()->getClientId() ) && ( ! $fullAccess ) ) {
			$isSave									=	( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) );
			$isProfile								=	( $reason == 'profile' );
		} elseif ( Application::Cms()->getClientId() && $this->params->get( 'cond_backend', false, GetterInterface::BOOLEAN ) && ( ! $fullAccess ) ) {
			$isSave									=	( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'apply', 'save' ) ) );
			$isProfile								=	( $reason == 'profile' );
		} else {
			$isSave									=	false;
			$isProfile								=	false;
		}

		if ( ( $isSave || $isProfile ) && $fields && ( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) ) ) {
			$tabs									=	array();
			$hide									=	array();

			foreach ( $fields as $field ) {
				if ( ! in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabs ) ) {
					$tabs[]							=	$field->get( 'tabid', 0, GetterInterface::INT );
				}
			}

			if ( $tabs ) {
				$tabCondition						=	$this->getTabConditional( $tabs, $reason, $user->get( 'id', 0, GetterInterface::INT ) );

				if ( $tabCondition ) {
					foreach ( $fields as $field ) {
						if ( in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabCondition->hide ) ) {
							$hide[]					=	$field->get( 'fieldid', 0, GetterInterface::INT );
						}
					}
				}
			}

			$condition								=	$this->getFieldConditional( $fields, $reason, $user->get( 'id', 0, GetterInterface::INT ) );

			if ( $condition->hide ) {
				foreach ( $fields as $field ) {
					if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $condition->hide ) ) {
						$hide[]						=	$field->get( 'fieldid', 0, GetterInterface::INT );
					}
				}
			}

			if ( $hide ) {
				foreach ( $fields as $k => $field ) {
					if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $hide ) ) {
						if ( $isSave && $reset ) {
							$fieldName				=	$field->get( 'name', null, GetterInterface::STRING );

							switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
								case 'date':
									if ( isset( $user->$fieldName ) ) {
										$user->set( $fieldName, '0000-00-00' );
									}
									break;
								case 'datetime':
									if ( isset( $user->$fieldName ) ) {
										$user->set( $fieldName, '0000-00-00 00:00:00' );
									}
									break;
								case 'integer':
								case 'points':
								case 'rating':
								case 'checkbox':
								case 'terms':
								case 'counter':
									if ( isset( $user->$fieldName ) ) {
										$user->set( $fieldName, 0 );
									}
									break;
								case 'image':
									if ( isset( $user->$fieldName ) ) {
										$user->set( $fieldName, '' );
									}

									$approvedName	=	$fieldName . 'approved';

									if ( isset( $user->$approvedName ) ) {
										$user->set( $approvedName, 0 );
									}
									break;
								default:
									foreach ( $field->getTableColumns() as $column ) {
										if ( isset( $user->$column ) ) {
											$user->set( $column, '' );
										}
									}
									break;
							}
						}

						unset( $fields[$k] );
					}
				}
			}
		}
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function fieldDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return							=	null;

		if ( ( ! $field->get( '_noCondition', false, GetterInterface::BOOLEAN  ) ) && ( ( ! Application::Cms()->getClientId() ) || $this->params->get( 'cond_backend', false, GetterInterface::BOOLEAN ) ) ) {
			$field->set( '_noCondition', true );

			if ( $output == 'html' ) {
				$tabCondition			=	$this->getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), $reason, $user->get( 'id', 0, GetterInterface::INT ) );
				$display				=	true;

				if ( in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabCondition->hide ) ) {
					$display			=	false;
				}

				if ( $display ) {
					$condition			=	$this->getFieldConditional( null, $reason, $user->get( 'id', 0, GetterInterface::INT ) );

					if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $condition->hide ) ) {
						$display		=	false;
					}
				}

				if ( ! $display ) {
					$return				=	' ';
				}
			} elseif ( $output == 'htmledit' ) {
				$tabCondition			=	$this->getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), $reason, $user->get( 'id', 0, GetterInterface::INT ) );
				$display				=	true;

				if ( in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabCondition->hideStatic ) ) {
					$display			=	false;
				} elseif ( ( $formatting == 'none' ) && in_array( $field->get( 'tabid', 0, GetterInterface::INT ), $tabCondition->hide ) ) {
					$display			=	false;
				}

				if ( $display ) {
					$condition			=	$this->getFieldConditional( $field, $reason, $user->id, ( $formatting != 'none' ) );

					if ( in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $condition->hideStatic ) ) {
						$display		=	false;
					} elseif ( ( $formatting == 'none' ) && in_array( $field->get( 'fieldid', 0, GetterInterface::INT ), $condition->hide ) ) {
						$display		=	false;
					}
				}

				if ( ! $display ) {
					$return				=	' ';
				}
			}

			$field->set( '_noCondition', false );
		}

		return $return;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $control_name
	 * @return array
	 */
	public function loadFields( $name, $value, $control_name )
	{
 		global $_CB_database;

		$values			=	array();

		$query			=	"SELECT CONCAT_WS( ',', f." . $_CB_database->NameQuote( 'fieldid' ) . ", f." . $_CB_database->NameQuote( 'name' ) . " ) AS value"
						.	", f." . $_CB_database->NameQuote( 'title' ) . " AS text"
						.	", f." . $_CB_database->NameQuote( 'name' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
						.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
						.	" ON t." . $_CB_database->NameQuote( 'tabid' ) . " = f." . $_CB_database->NameQuote( 'tabid' )
						.	"\n WHERE f." . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	"\n AND f." . $_CB_database->NameQuote( 'name' ) . " != " . $_CB_database->Quote( 'NA' )
						.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' );
		$_CB_database->setQuery( $query );
		$fields			=	$_CB_database->loadObjectList();

		foreach ( $fields as $field ) {
			$values[]	=	moscomprofilerHTML::makeOption( $field->value, CBTxt::T( $field->text ) . ' (' . $field->name . ')' );
		}

		return $values;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $control_name
	 * @return array
	 */
	public function loadFieldOptions( $name, $value, $control_name )
	{
 		global $_CB_database;

		$fields				=	array();
		$values				=	array();

		$query				=	'SELECT o.' . $_CB_database->NameQuote( 'fieldvalueid' ) . ' AS value'
							.	', IF( o.' . $_CB_database->NameQuote( 'fieldlabel' ) . ' != "", o.' . $_CB_database->NameQuote( 'fieldlabel' ) . ', o.' . $_CB_database->NameQuote( 'fieldtitle' ) . ' ) AS text'
							.	', o.' . $_CB_database->NameQuote( 'fieldid' )
							.	', f.' . $_CB_database->NameQuote( 'title' ) . ' AS field_title'
							.	', f.' . $_CB_database->NameQuote( 'name' ) . ' AS field_name'
							. 	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_field_values' ) . " AS o"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
							.	' ON f.' . $_CB_database->NameQuote( 'fieldid' ) . ' = o.' . $_CB_database->NameQuote( 'fieldid' )
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
							.	' ON t.' . $_CB_database->NameQuote( 'tabid' ) . ' = f.' . $_CB_database->NameQuote( 'tabid' )
							.	"\n WHERE f." . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	"\n AND f." . $_CB_database->NameQuote( 'name' ) . " != " . $_CB_database->Quote( 'NA' )
							.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'title' ) . ", o." . $_CB_database->NameQuote( 'ordering' );
		$_CB_database->setQuery( $query );
		$options			=	$_CB_database->loadObjectList();

		if ( $options ) foreach( $options as $option ) {
			if ( ! in_array( $option->fieldid, $fields ) ) {
				$values[]	=	moscomprofilerHTML::makeOptGroup( CBTxt::T( $option->field_title ) );
				$fields[]	=	$option->fieldid;
			}

			$values[]		=	moscomprofilerHTML::makeOption( $option->value, CBTxt::T( $option->text ) );
		}

		return $values;
	}
}