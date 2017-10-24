<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/17/14 11:22 PM $
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

/**
 * cbCalendars Class implementation
 * Calendars Class for date fields handler
 */
class cbCalendars
{
	/**
	 * Application 1=Front End 2=Admin
	 * @var int
	 */
	protected $ui					=	0;
	/**
	 * Default Date Format
	 * @var string
	 */
	protected $defDateFormat;
	/**
	 * Date Format
	 * @var array
	 */
	protected $dateFormat;
	/**
	 * Default Time Format
	 * @var string
	 */
	protected $defTimeFormat;
	/**
	 * Time Format
	 * @var array
	 */
	protected $timeFormat;
	/**
	 * Calendar type: 1=popup 2=jason's
	 * @var int
	 */
	protected $calendarType;

	/**
	 * Constructor
	 * Includes files needed for displaying calendar for date fields
	 *
	 * @param  int     $ui            User interface: 1 = Front End, 2 = Admin
	 * @param  int     $calendarType  Calendar type: 1 = popup only with input, 2 = drop downs with popup, 3 = drop downs without popup, 4 = popup only without input, null = config
	 * @param  string  $dateFormat    Default date format: overrides the default date format provided by configuration
	 * @param  string  $timeFormat    Default time format: overrides the default time format provided by configuration
	 */
	public function __construct( $ui, $calendarType = null, $dateFormat = null, $timeFormat = null )
	{
		global $_CB_framework, $ueConfig;

		$this->ui						=	$ui;
		$this->calendarType				=	( $calendarType ? $calendarType : ( isset( $ueConfig['calendar_type'] ) ? $ueConfig['calendar_type'] : 2 ) );
		$this->defDateFormat			=	( $dateFormat ? $dateFormat : $ueConfig['date_format'] );
		$this->defTimeFormat			=	( $timeFormat ? $timeFormat : ( CBTxt::T( 'UE_TIME_FORMAT', '' ) != '' ? CBTxt::T( 'UE_TIME_HOUR', '' ) : ( isset( $ueConfig['time_format'] ) ? $ueConfig['time_format'] : 'H:i:s' ) ) );
		$this->dateFormat				=	array();
		$this->timeFormat				=	array();

		// Popup formats:
		$this->dateFormat[1]			=	array();

		// Popup date template:
		$this->dateFormat[1][1]			=	$this->formatToTemplate( $this->defDateFormat, 'popup' );

		// Popup date sql format:
		$this->dateFormat[1][2]			=	'yy-mm-dd'; // Y-m-d

		// Popup time template:
		$this->timeFormat[1][1]			=	$this->formatToTemplate( $this->defTimeFormat, 'popup' );

		// Popup time sql format:
		$this->timeFormat[1][2]			=	'HH:mm:ss'; // H:i:s

		// Dropdown formats:
		$this->dateFormat[2]			=	array();

		// Dropdown date template:
		$this->dateFormat[2][1]			=	$this->formatToTemplate( $this->defDateFormat );

		// Dropdown date sql format:
		$this->dateFormat[2][2]			=	'YYYY-MM-DD'; // Y-m-d

		$this->timeFormat[2]			=	array();

		// Dropdown time template:
		$this->timeFormat[2][1]			=	$this->formatToTemplate( $this->defTimeFormat );

		// Dropdown time sql format:
		$this->timeFormat[2][2]			=	'HH:mm:ss'; // H:i:s

		static $JS_loaded				=	0;

		if ( ! $JS_loaded++ ) {
			$messages					=	array(	'amNames' => array(
														addslashes( CBTxt::T( 'UE_HALF_DAY_AM', 'AM' ) ),
														addslashes( CBTxt::T( 'UE_HALF_DAY_MIN_AM', 'A' ) )
													),
													'pmNames' => array(
														addslashes( CBTxt::T( 'UE_HALF_DAY_PM', 'PM' ) ),
														addslashes( CBTxt::T( 'UE_HALF_DAY_MIN_PM', 'P' ) )
													),
													'dayNames' => array(
														addslashes( CBTxt::T( 'UE_WEEKDAYS_1', 'Sunday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_2', 'Monday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_3', 'Tuesday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_4', 'Wednesday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_5', 'Thursday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_6', 'Friday' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_7', 'Saturday' ) )
													),
													'dayNamesMin' => array(
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_1', 'Su' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_2', 'Mo' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_3', 'Tu' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_4', 'We' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_5', 'Th' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_6', 'Fr' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_MIN_7', 'Sa' ) )
													),
													'dayNamesShort' => array(
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_1', 'Sun' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_2', 'Mon' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_3', 'Tue' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_4', 'Wed' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_5', 'Thu' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_6', 'Fri' ) ),
														addslashes( CBTxt::T( 'UE_WEEKDAYS_SHORT_7', 'Sat' ) )
													),
													'monthNames' => array(
														addslashes( CBTxt::T( 'UE_MONTHS_1', 'January' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_2', 'February' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_3', 'March' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_4', 'April' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_5', 'May' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_6', 'June' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_7', 'July' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_8', 'August' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_9', 'September' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_10', 'October' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_11', 'November' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_12', 'December' ) )
													),
													'monthNamesShort' => array(
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_1', 'Jan' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_2', 'Feb' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_3', 'Mar' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_4', 'Apr' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_5', 'May' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_6', 'Jun' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_7', 'Jul' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_8', 'Aug' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_9', 'Sep' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_10', 'Oct' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_11', 'Nov' ) ),
														addslashes( CBTxt::T( 'UE_MONTHS_SHORT_12', 'Dec' ) )
													),
													'prevText' => addslashes( CBTxt::T( 'UE_PREV_PAGE', 'Prev' ) ),
													'nextText' => addslashes( CBTxt::T( 'UE_NEXT_PAGE', 'Next' ) ),
													'currentText' => addslashes( CBTxt::T( 'UE_NOW', 'Now' ) ),
													'closeText' => addslashes( CBTxt::T( 'UE_CALENDAR_CLOSE_DONE', 'Done' ) ),
													'timeOnlyTitle' => addslashes( CBTxt::T( 'UE_CHOOSE_TIME', 'Choose Time' ) ),
													'timeText' => addslashes( CBTxt::T( 'UE_TIME_TIME', 'Time' ) ),
													'hourText' => addslashes( CBTxt::T( 'UE_TIME_HOUR', 'Hour' ) ),
													'minuteText' => addslashes( CBTxt::T( 'UE_TIME_MINUTE', 'Minute' ) ),
													'secondText' => addslashes( CBTxt::T( 'UE_TIME_SECOND', 'Second' ) ),
													'millisecText' => addslashes( CBTxt::T( 'UE_TIME_MILLISECOND', 'Millisecond' ) ),
													'microsecText' => addslashes( CBTxt::T( 'UE_TIME_MICROSECOND', 'Microsecond' ) ),
													'timezoneText' => addslashes( CBTxt::T( 'UE_TIME_TIMEZONE', 'Timezone' ) )
												);

			$options					=	array( 'strings' => $messages, 'customClass' => 'form-control' );

			$_CB_framework->outputCbJQuery( "$( '.cbDatePicker' ).cbdatepicker(" . json_encode( $options ) . ");", 'cbdatepicker' );
		}
	}

	/**
	 * Converts PHP date format to calendar template
	 *
	 * @param string $format
	 * @param string $calendar
	 * @return string
	 */
	private function formatToTemplate( $format, $calendar = 'dropdown' )
	{
		// From PHP to generic:
		$replace			=	array(	'd'	=>	'{#01}',	'D'	=>	'{#02}',	'j'	=>	'{#03}',	'l'	=>	'{#04}',	'N'	=>	'{#05}',
										'S'	=>	'{#06}',	'w'	=>	'{#07}',	'z'	=>	'{#08}',	'W'	=>	'{#09}',	'F'	=>	'{#10}',
										'm'	=>	'{#11}',	'M'	=>	'{#12}',	'n'	=>	'{#13}',	't'	=>	'{#14}',	'L'	=>	'{#15}',
										'o'	=>	'{#16}',	'Y'	=>	'{#17}',	'y'	=>	'{#18}',	'a'	=>	'{#19}',	'A'	=>	'{#20}',
										'B'	=>	'{#21}',	'g'	=>	'{#22}',	'G'	=>	'{#23}',	'h'	=>	'{#24}',	'H'	=>	'{#25}',
										'i'	=>	'{#26}',	's'	=>	'{#27}',	'u'	=>	'{#28}',	'v'	=>	'{#29}',	'e'	=>	'{#30}',
										'I'	=>	'{#31}',	'O'	=>	'{#32}',	'P'	=>	'{#33}',	'T'	=>	'{#34}',	'Z'	=>	'{#35}',
										'c'	=>	'{#36}',	'r'	=>	'{#47}',	'U'	=>	'{#38}'
									);

		$format				=	str_replace( array_keys( $replace ), array_values( $replace ), $format );

		if ( $calendar == 'popup' ) {
			// From generic to datepicker:
			$replace		=	array(	'{#01}'	=>	'dd',	'{#02}'	=>	'D',	'{#03}'	=>	'd',	'{#04}'	=>	'DD',	'{#05}'	=>	'',
										'{#06}'	=>	'',		'{#07}'	=>	'',		'{#08}'	=>	'o',	'{#09}'	=>	'',		'{#10}'	=>	'MM',
										'{#11}'	=>	'mm',	'{#12}'	=>	'M',	'{#13}'	=>	'm',	'{#14}'	=>	'',		'{#15}'	=>	'',
										'{#16}'	=>	'',		'{#17}'	=>	'yy',	'{#18}'	=>	'y',	'{#19}'	=>	'tt',	'{#20}'	=>	'TT',
										'{#21}'	=>	'',		'{#22}'	=>	'h',	'{#23}'	=>	'H',	'{#24}'	=>	'hh',	'{#25}'	=>	'HH',
										'{#26}'	=>	'mm',	'{#27}'	=>	'ss',	'{#28}'	=>	'c',	'{#29}'	=>	'l',	'{#30}'	=>	'',
										'{#31}'	=>	'',		'{#32}'	=>	'',		'{#33}'	=>	'',		'{#34}'	=>	'',		'{#35}'	=>	'',
										'{#36}'	=>	'',		'{#37}'	=>	'',		'{#38}'	=>	''
									);

			$format			=	str_replace( array_keys( $replace ), array_values( $replace ), $format );
		} elseif ( $calendar == 'dropdown' ) {
			// From generic to combodate:
			$replace		=	array(	'{#01}'	=>	'DD',	'{#02}'	=>	'',		'{#03}'	=>	'D',	'{#04}'	=>	'',		'{#05}'	=>	'',
										'{#06}'	=>	'',		'{#07}'	=>	'',		'{#08}'	=>	'DDD',	'{#09}'	=>	'',		'{#10}'	=>	'MMMM',
										'{#11}'	=>	'MM',	'{#12}'	=>	'MMM',	'{#13}'	=>	'M',	'{#14}'	=>	'',		'{#15}'	=>	'',
										'{#16}'	=>	'',		'{#17}'	=>	'YYYY',	'{#18}'	=>	'YY',	'{#19}'	=>	'a',	'{#20}'	=>	'A',
										'{#21}'	=>	'',		'{#22}'	=>	'h',	'{#23}'	=>	'H',	'{#24}'	=>	'hh',	'{#25}'	=>	'HH',
										'{#26}'	=>	'mm',	'{#27}'	=>	'ss',	'{#28}'	=>	'',		'{#29}'	=>	'',		'{#30}'	=>	'',
										'{#31}'	=>	'',		'{#32}'	=>	'',		'{#33}'	=>	'',		'{#34}'	=>	'',		'{#35}'	=>	'',
										'{#36}'	=>	'',		'{#37}'	=>	'',		'{#38}'	=>	'',		'/'		=>	' / ',	'-'		=>	' - ',
										'.'		=>	' . ',	':'		=>	' : '
									);

			$format			=	str_replace( array_keys( $replace ), array_values( $replace ), $format );
		}

		return $format;
	}

	/**
	 * Outputs calendar driven field
	 *
	 * @param  string          $name             Name of field
	 * @param  null|string     $label            Label of field
	 * @param  boolean         $required         Is required ?
	 * @param  null|string     $value            Current value
	 * @param  boolean         $readOnly         Read-only field ?
	 * @param  boolean         $showTime         Show time too ?
	 * @param  null|int        $minYear          Minimum year to display
	 * @param  null|int        $maxYear          Maximum year to display
	 * @param  null|string     $attributes       Other HTML attributes
	 * @param  boolean         $serverTimeOffset False: don't offset, true: offset if time also in $date
	 * @param  null|string|int $offsetOverride   Offset override for time display
	 * @return string                    HTML for calendar
	 */
	public function cbAddCalendar( $name, $label = null, $required = false, $value = null, $readOnly = false, $showTime = false, $minYear = null, $maxYear = null, $attributes = null, $serverTimeOffset = true, $offsetOverride = null )
	{
		global $_CB_framework;

		if ( ( ! $value ) || ( $value == '0000-00-00 00:00:00' ) || ( $value == '0000-00-00' ) ) {
			if ( $showTime ) {
				$value						=	'0000-00-00 00:00:00';
			} else {
				$value						=	'0000-00-00';
			}
		} else {
			// Something sent an invalid format length; lets pad the date to fix this:
			if ( strlen( $value ) < 10 ) {
				if ( strlen( $value ) < 5 ) {
					// We seam to only have the year; lets give fake month-day:
					$value					=	$value . '-01-01';
				} else {
					if ( preg_match( '/\d{4}-\d+/i', $value ) ) {
						// We seam to have year and month; lets fake day:
						$value				=	rtrim( $value, '-' ) . '-01';
					} else {
						// We seam to have month-day: lets fake the year:
						$value				=	'1970-' . ltrim( $value, '-' );
					}
				}
			}

			// We always expect an SQL formatted date or timestamp; if it's anything otherwise then this function was used wrong:
			$value							=	Application::Date( $value, 'UTC' )->format( 'Y-m-d' . ( $showTime ? ' H:i:s' : null ) );
		}

		if ( ( ! $value ) || ( $value == '0000-00-00 00:00:00' ) || ( $value == '0000-00-00' ) ) {
			$isEmpty						=	true;
			$value							=	'';
		} else {
			$isEmpty						=	false;
		}

		// Initially set the offset value to the current value and we'll pass it through offset parsing if we need to:
		$offsetValue						=	$value;

		if ( ( $this->calendarType == 2 ) && ( ! $readOnly ) ) {
			$addPopup						=	true;
		} else {
			$addPopup						=	false;
		}

		$return								=	null;

		// When name is missing the bindings break so lets just make one:
		if ( ! $name ) {
			$name							=	uniqid();
		}

		$inputId							=	moscomprofilerHTML::htmlId( $name );

		if ( $readOnly ) {
			$return							=	htmlspecialchars( $value );
		} else {
			$attributes						.=	' data-cbdatepicker-calendartype="' . (int) $this->calendarType . '"'
											.	' data-cbdatepicker-target="#' . htmlspecialchars( $inputId ) . '"';

			if ( ( $minYear !== null ) && ( $maxYear !== null ) ) {
				$attributes					.=	' data-cbdatepicker-minyear="' . (int) $minYear . '" data-cbdatepicker-maxyear="' . (int) $maxYear . '"';
			}

			if ( $_CB_framework->document->getDirection() == 'rtl' ) {
				$attributes					.=	' data-cbdatepicker-isrtl="true"';
			}

			// This determines the final format of the date or datetime for storage (set in the hidden input):
			$attributes						.=	' data-cbdatepicker-format="' . htmlspecialchars( $this->dateFormat[2][2] . ( $showTime ? ' ' . $this->timeFormat[2][2] : null ) ) . '"';

			if ( in_array( $this->calendarType, array( 2, 3 ) ) ) {
				$tooltipTarget				=	'#' . htmlspecialchars( $inputId ) . 'Picker + .combodate';

				$attributes					.=	' data-cbtooltip-open-target="' . $tooltipTarget . '" data-cbtooltip-close-target="' . $tooltipTarget . '" data-cbtooltip-position-target="' . $tooltipTarget . '"'
											.	' data-cbdatepicker-template="' . htmlspecialchars( $this->dateFormat[2][1] . ( $showTime ? ' ' . $this->timeFormat[2][1] : null ) ) . '"';

				if ( $required && ( ! $isEmpty ) ) {
					$attributes				.=	' data-cbdatepicker-firstitem="none"';
				}
			}

			if ( in_array( $this->calendarType, array( 1, 4 ) ) || $addPopup ) {
				$return						=	'&nbsp;&nbsp;<span id="' . htmlspecialchars( $inputId ) . 'Calendar" class="cbDatePickerCalendar hasCalendar fa fa-calendar" title="' . htmlspecialchars( CBTxt::T( 'UE_CALENDAR_TITLE', 'Calendar' ) ) . '"></span>';

				if ( $showTime ) {
					$attributes				.=	' data-cbdatepicker-showtime="true" data-cbdatepicker-timeformat="' . htmlspecialchars( $this->timeFormat[1][2] ) . '"';
				}

				if ( $addPopup ) {
					$attributes				.=	' data-cbdatepicker-addpopup="true"';
				}

				$firstDay					=	CBTxt::T( 'UE_CALENDAR_FIRSTDAY', '' );

				if ( $firstDay != '' ) {
					$attributes				.=	' data-cbdatepicker-firstday="' . (int) CBTxt::T( 'UE_CALENDAR_FIRSTDAY', '' ) . '"';
				}

				$attributes					.=	' data-cbdatepicker-dateformat="' . htmlspecialchars( $this->dateFormat[1][2] ) . '"';

				if ( in_array( $this->calendarType, array( 1, 4 ) ) ) {
					$attributes				.=	' data-cbdatepicker-datetemplate="' . htmlspecialchars( $this->dateFormat[1][1] ) . '"';

					if ( $showTime ) {
						$attributes			.=	' data-cbdatepicker-timetemplate="' . htmlspecialchars( $this->timeFormat[1][1] ) . '"';
					}
				}
			}

			// If server time offset is enabled then tell jquery the offset in minutes:
			if ( $showTime && $serverTimeOffset ) {
				$offset						=	( $offsetOverride !== null ? $offsetOverride : $_CB_framework->getCfg( 'user_timezone' ) );

				// Ignore offset entirely if there is no offset value or it's UTC (we're already in UTC):
				if ( $offset && ( $offset != 'UTC' ) ) {
					// If the date has a time then offset it and send it to the jquery:
					if ( ( strlen( $offsetValue ) > 10 ) && $offsetValue ) {
						$offsetValue		=	Application::Date( $offsetValue, $offset )->format( 'Y-m-d H:i:s' );
					}

					// Pass the timezone name for momentjs-timezone:
					$attributes				.=	' data-cbdatepicker-timezone="' . htmlspecialchars( $offset ) . '"';
				}
			}

			if ( in_array( $this->calendarType, array( 1, 4 ) ) ) {
				$offsetValue				=	Application::Date( $offsetValue, 'UTC' )->format( $this->defDateFormat . ( $showTime ? ' ' . $this->defTimeFormat : null ) );
			}

			$return							=	'<input type="hidden" name="' . htmlspecialchars( $name ) . '" id="' . htmlspecialchars( $inputId ) . '" value="' . htmlspecialchars( $value ) . '" />'
											.	'<input type="text" id="' . htmlspecialchars( $inputId ) . 'Picker" value="' . htmlspecialchars( $offsetValue ) . '" class="cbDatePicker form-control' . ( $required ? ' required' : null ) . ( $this->calendarType == 4 ? ' hidden' : null ) . '"' . ( $readOnly ? ' disabled="disabled"' : null ) . ( $label ? ' title="' . htmlspecialchars( $label ) . '"' : null ) . ( trim( $attributes ) ? ' ' . $attributes : null ) . ' />'
											.	( $this->calendarType == 4 ? '<span id="' . htmlspecialchars( $inputId ) . 'Selected" class="cbDatePickerSelected">' . htmlspecialchars( $offsetValue ) . '</span>' : null )
											.	$return;
		}

		return $return;
	}
}
