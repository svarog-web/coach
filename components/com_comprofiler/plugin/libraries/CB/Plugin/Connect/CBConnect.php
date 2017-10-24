<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class CBConnect
{

	/**
	 * Returns the current return url or generates one from current page
	 *
	 * @param bool|false $current
	 * @param bool|false $raw
	 * @return null|string
	 */
	static public function getReturn( $current = false, $raw = false )
	{
		static $cache				=	array();

		if ( ! isset( $cache[$current] ) ) {
			$url					=	null;

			if ( $current ) {
				$returnUrl			=	Application::Input()->get( 'get/return', '', GetterInterface::BASE64 );

				if ( $returnUrl ) {
					$returnUrl		=	base64_decode( $returnUrl );

					if ( \JUri::isInternal( $returnUrl ) ) {
						$url		=	$returnUrl;
					}
				}
			} else {
				$isHttps			=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
				$returnUrl			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

				if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
					$returnUrl		.=	$_SERVER['REQUEST_URI'];
				} else {
					$returnUrl		.=	$_SERVER['SCRIPT_NAME'];

					if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
						$returnUrl	.=	'?' . $_SERVER['QUERY_STRING'];
					}
				}

				$url				=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $returnUrl ) ) ) ) );
			}

			$cache[$current]		=	$url;
		}

		$return						=	$cache[$current];

		if ( ( ! $raw ) && $return ) {
			$return					=	base64_encode( $return );
		}

		return $return;
	}

	/**
	 * Redirects to the return url if available otherwise to the url specified
	 *
	 * @param string      $provider
	 * @param string      $url
	 * @param null|string $message
	 * @param string      $messageType
	 */
	static public function returnRedirect( $provider, $url, $message = null, $messageType = 'message' )
	{
		$returnUrl			=	null;

		if ( $provider ) {
			$session		=	Application::Session();

			if ( $session->get( $provider . '.return' ) ) {
				$returnUrl	=	base64_decode( $session->get( $provider . '.return' ) );
			}
		}

		if ( ! $returnUrl ) {
			$returnUrl		=	self::getReturn( true, true );
		}

		if ( ! $returnUrl ) {
			$returnUrl		=	$url;
		}

		cbRedirect( $returnUrl, $message, $messageType );
	}

	/**
	 * Returns an array of available CB Connect providers
	 *
	 * @return array
	 */
	static public function getProviders()
	{
		return array(	'facebook'		=>	array(	'name'			=>	CBTxt::T( 'Facebook' ),
													'field'			=>	'fb_userid',
													'icon'			=>	'facebook',
													'button'		=>	'primary',
													'profile'		=>	'https://www.facebook.com/app_scoped_user_id/[id]',
													'permissions'	=>	array(	'user_friends', 'user_about_me', 'user_actions.books', 'user_actions.fitness', 'user_actions.music',
																				'user_actions.news', 'user_actions.video', 'user_birthday', 'user_education_history', 'user_events',
																				'user_games_activity', 'user_hometown', 'user_likes', 'user_location', 'user_managed_groups', 'user_photos',
																				'user_posts', 'user_relationships', 'user_relationship_details', 'user_religion_politics', 'user_tagged_places',
																				'user_videos', 'user_website', 'user_work_history', 'read_custom_friendlists', 'read_insights', 'read_audience_network_insights',
																				'read_page_mailboxes', 'manage_pages', 'publish_pages', 'publish_actions', 'rsvp_event', 'pages_show_list', 'pages_manage_cta', 'ads_read', 'ads_management'
																			),
													'fields'		=>	array(	'id', 'about', 'bio', 'birthday', 'email', 'first_name', 'gender', 'last_name', 'link', 'locale', 'middle_name',
																				'name', 'name_format', 'political', 'relationship_status', 'religion', 'quotes', 'timezone', 'updated_time', 'verified', 'website'
																			),
													'ssl'			=>	false
												),
						'twitter'		=>	array(	'name'			=>	CBTxt::T( 'Twitter' ),
													'field'			=>	'twitter_userid',
													'icon'			=>	'twitter text-info',
													'button'		=>	'default',
													'profile'		=>	'https://twitter.com/intent/user?user_id=[id]',
													'permissions'	=>	array(),
													'fields'		=>	array(	'id', 'name', 'screen_name', 'location', 'description', 'url', 'protected', 'followers_count', 'friends_count', 'listed_count',
																				'created_at', 'favourites_count', 'utc_offset', 'time_zone', 'geo_enabled', 'verified', 'statuses_count', 'lang', 'contributors_enabled',
																				'is_translator', 'is_translation_enabled', 'profile_background_color', 'profile_background_image_url', 'profile_background_image_url_https',
																				'profile_background_tile', 'profile_image_url', 'profile_image_url_https', 'profile_banner_url', 'profile_link_color', 'profile_sidebar_border_color',
																				'profile_sidebar_fill_color', 'profile_text_color', 'profile_use_background_image', 'has_extended_profile', 'default_profile', 'default_profile_image',
																				'following', 'follow_request_sent', 'notifications'
																			),
													'ssl'			=>	false
												),
						'google'		=>	array(	'name'			=>	CBTxt::T( 'Google' ),
													'field'			=>	'google_userid',
													'icon'			=>	'google-plus',
													'button'		=>	'danger',
													'profile'		=>	'https://plus.google.com/[id]',
													'permissions'	=>	array(	'https://www.googleapis.com/auth/activity', 'https://www.googleapis.com/auth/adexchange.buyer', 'https://www.googleapis.com/auth/adexchange.seller',
																				'https://www.googleapis.com/auth/adexchange.seller.readonly', 'https://www.googleapis.com/auth/admin.directory.group', 'https://www.googleapis.com/auth/admin.directory.user',
																				'https://www.googleapis.com/auth/admin.reports.audit.readonly', 'https://www.googleapis.com/auth/admin.reports.usage.readonly', 'https://www.googleapis.com/auth/adsense',
																				'https://www.googleapis.com/auth/adsense.readonly', 'https://www.googleapis.com/auth/adsensehost', 'https://www.googleapis.com/auth/analytics', 'https://www.googleapis.com/auth/analytics.edit',
																				'https://www.googleapis.com/auth/analytics.manage.users', 'https://www.googleapis.com/auth/analytics.manage.users.readonly', 'https://www.googleapis.com/auth/analytics.provision',
																				'https://www.googleapis.com/auth/analytics.readonly', 'https://www.googleapis.com/auth/androidenterprise', 'https://www.googleapis.com/auth/androidpublisher',
																				'https://www.googleapis.com/auth/appengine.admin', 'https://www.googleapis.com/auth/apps.groups.migration', 'https://www.googleapis.com/auth/apps.groups.settings',
																				'https://www.googleapis.com/auth/apps.licensing', 'https://www.googleapis.com/auth/apps.order', 'https://www.googleapis.com/auth/apps.order.readonly', 'https://www.googleapis.com/auth/appstate',
																				'https://www.googleapis.com/auth/bigquery', 'https://www.googleapis.com/auth/bigquery.insertdata', 'https://www.googleapis.com/auth/blogger', 'https://www.googleapis.com/auth/blogger.readonly',
																				'https://www.googleapis.com/auth/books', 'https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.readonly', 'https://www.googleapis.com/auth/classroom.courses',
																				'https://www.googleapis.com/auth/classroom.courses.readonly', 'https://www.googleapis.com/auth/classroom.profile.emails', 'https://www.googleapis.com/auth/classroom.profile.photos',
																				'https://www.googleapis.com/auth/classroom.rosters', 'https://www.googleapis.com/auth/classroom.rosters.readonly', 'https://www.googleapis.com/auth/cloud-platform',
																				'https://www.googleapis.com/auth/cloud-platform.read-only', 'https://www.googleapis.com/auth/cloud.useraccounts', 'https://www.googleapis.com/auth/cloud.useraccounts.readonly',
																				'https://www.googleapis.com/auth/cloud_debugger', 'https://www.googleapis.com/auth/cloud_debugletcontroller', 'https://www.googleapis.com/auth/compute',
																				'https://www.googleapis.com/auth/compute.readonly', 'https://www.googleapis.com/auth/content', 'https://www.googleapis.com/auth/coordinate', 'https://www.googleapis.com/auth/coordinate.readonly',
																				'https://www.googleapis.com/auth/datastore', 'https://www.googleapis.com/auth/devstorage.full_control', 'https://www.googleapis.com/auth/devstorage.read_only',
																				'https://www.googleapis.com/auth/devstorage.read_write', 'https://www.googleapis.com/auth/dfareporting', 'https://www.googleapis.com/auth/dfatrafficking',
																				'https://www.googleapis.com/auth/doubleclicksearch', 'https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/drive.appdata', 'https://www.googleapis.com/auth/drive.file',
																				'https://www.googleapis.com/auth/drive.metadata', 'https://www.googleapis.com/auth/drive.metadata.readonly', 'https://www.googleapis.com/auth/drive.photos.readonly',
																				'https://www.googleapis.com/auth/drive.readonly', 'https://www.googleapis.com/auth/drive.scripts', 'https://www.googleapis.com/auth/fitness.activity.read',
																				'https://www.googleapis.com/auth/fitness.activity.write', 'https://www.googleapis.com/auth/fitness.body.read', 'https://www.googleapis.com/auth/fitness.body.write',
																				'https://www.googleapis.com/auth/fitness.location.read', 'https://www.googleapis.com/auth/fitness.location.write', 'https://www.googleapis.com/auth/forms',
																				'https://www.googleapis.com/auth/forms.currentonly', 'https://www.googleapis.com/auth/fusiontables', 'https://www.googleapis.com/auth/fusiontables.readonly',
																				'https://www.googleapis.com/auth/games', 'https://www.googleapis.com/auth/genomics', 'https://www.googleapis.com/auth/genomics.readonly', 'https://www.googleapis.com/auth/glass.location',
																				'https://www.googleapis.com/auth/glass.timeline', 'https://www.googleapis.com/auth/gmail.compose', 'https://www.googleapis.com/auth/gmail.insert', 'https://www.googleapis.com/auth/gmail.labels',
																				'https://www.googleapis.com/auth/gmail.modify', 'https://www.googleapis.com/auth/gmail.readonly', 'https://www.googleapis.com/auth/gmail.send', 'https://www.googleapis.com/auth/groups',
																				'https://www.googleapis.com/auth/logging.admin', 'https://www.googleapis.com/auth/logging.read', 'https://www.googleapis.com/auth/logging.write', 'https://www.googleapis.com/auth/monitoring',
																				'https://www.googleapis.com/auth/monitoring.readonly', 'https://www.googleapis.com/auth/ndev.clouddns.readonly', 'https://www.googleapis.com/auth/ndev.clouddns.readwrite',
																				'https://www.googleapis.com/auth/ndev.cloudman', 'https://www.googleapis.com/auth/ndev.cloudman.readonly', 'https://www.googleapis.com/auth/playmovies_partner.readonly',
																				'https://www.googleapis.com/auth/plus.circles.read', 'https://www.googleapis.com/auth/plus.circles.write', 'https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/plus.me',
																				'https://www.googleapis.com/auth/plus.media.upload', 'https://www.googleapis.com/auth/plus.profiles.read', 'https://www.googleapis.com/auth/plus.stream.read',
																				'https://www.googleapis.com/auth/plus.stream.write', 'https://www.googleapis.com/auth/prediction', 'https://www.googleapis.com/auth/pubsub', 'https://www.googleapis.com/auth/replicapool',
																				'https://www.googleapis.com/auth/replicapool.readonly', 'https://www.googleapis.com/auth/siteverification', 'https://www.googleapis.com/auth/siteverification.verify_only',
																				'https://www.googleapis.com/auth/sqlservice.admin', 'https://www.googleapis.com/auth/tagmanager.delete.containers', 'https://www.googleapis.com/auth/tagmanager.edit.containers',
																				'https://www.googleapis.com/auth/tagmanager.edit.containerversions', 'https://www.googleapis.com/auth/tagmanager.manage.accounts', 'https://www.googleapis.com/auth/tagmanager.manage.users',
																				'https://www.googleapis.com/auth/tagmanager.publish', 'https://www.googleapis.com/auth/tagmanager.readonly', 'https://www.googleapis.com/auth/taskqueue',
																				'https://www.googleapis.com/auth/taskqueue.consumer', 'https://www.googleapis.com/auth/tasks', 'https://www.googleapis.com/auth/tasks.readonly', 'https://www.googleapis.com/auth/urlshortener',
																				'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/webmasters',
																				'https://www.googleapis.com/auth/webmasters.readonly', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtube.force-ssl',
																				'https://www.googleapis.com/auth/youtube.readonly', 'https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtubepartner',
																				'https://www.googleapis.com/auth/youtubepartner-channel-audit', 'https://www.googleapis.com/auth/yt-analytics-monetary.readonly', 'https://www.googleapis.com/auth/yt-analytics.readonly'
																			),
													'fields'		=>	array(	'kind', 'id', 'displayName', 'name.formatted', 'name.familyName', 'name.givenName', 'name.middleName', 'name.honorificPrefix', 'name.honorificSuffix',
																				'nickname', 'birthday', 'gender', 'url', 'image.url', 'aboutMe', 'relationshipStatus', 'tagline', 'objectType', 'isPlusUser', 'braggingRights', 'plusOneCount',
																				'circledByCount', 'verified', 'cover.coverPhoto.url', 'language', 'ageRange.min', 'ageRange.max', 'emails.0.value', 'domain', 'occupation', 'skills'
																			),
													'ssl'			=>	false
												),
						'linkedin'		=>	array(	'name'			=>	CBTxt::T( 'LinkedIn' ),
													'field'			=>	'linkedin_userid',
													'icon'			=>	'linkedin',
													'button'		=>	'info',
													'profile'		=>	'https://www.linkedin.com/profile/view?id=[id]',
													'permissions'	=>	array(	'rw_company_admin', 'w_share' ),
													'fields'		=>	array(	'id', 'first-name', 'last-name', 'maiden-name', 'formatted-name', 'phonetic-first-name', 'phonetic-last-name', 'formatted-phonetic-name', 'headline', 'email-address',
																				'current-share', 'num-connections', 'num-connections-capped', 'summary', 'specialties', 'picture-url', 'site-standard-profile-request', 'api-standard-profile-request',
																				'public-profile-url'
																			),
													'ssl'			=>	false
												),
						'windowslive'	=>	array(	'name'			=>	CBTxt::T( 'Windows Live' ),
													'field'			=>	'windowslive_userid',
													'icon'			=>	'windows',
													'button'		=>	'default',
													'profile'		=>	'https://profile.live.com/[id]',
													'permissions'	=>	array(	'wl.offline_access', 'wl.birthday', 'wl.calendars', 'wl.calendars_update', 'wl.contacts_birthday', 'wl.contacts_create', 'wl.contacts_calendars', 'wl.contacts_photos',
																				'wl.contacts_skydrive', 'wl.events_create', 'wl.imap', 'wl.phone_numbers', 'wl.photos', 'wl.postal_addresses', 'wl.skydrive', 'wl.skydrive_update', 'wl.work_profile', 'office.onenote_create'
																			),
													'fields'		=>	array(	'id', 'name', 'first_name', 'last_name', 'link', 'birth_day', 'birth_month', 'birth_year', 'emails.preferred', 'emails.account', 'emails.personal', 'emails.business', 'emails.other',
																				'addresses.personal.street', 'addresses.personal.street_2', 'addresses.personal.city', 'addresses.personal.state', 'addresses.personal.postal_code', 'addresses.personal.region',
																				'addresses.business.street', 'addresses.business.street_2', 'addresses.business.city', 'addresses.business.state', 'addresses.business.postal_code', 'addresses.business.region',
																				'phones.personal', 'phones.business', 'phones.mobile', 'locale', 'updated_time'
																			),
													'ssl'			=>	false
												),
						'instagram'		=>	array(	'name'			=>	CBTxt::T( 'Instagram' ),
													'field'			=>	'instagram_userid',
													'icon'			=>	'instagram',
													'button'		=>	'warning',
													'profile'		=>	'http://instagram.com/[id]',
													'permissions'	=>	array(	'public_content', 'follower_list', 'comments', 'relationships', 'likes' ),
													'fields'		=>	array(	'id', 'username', 'full_name', 'profile_picture', 'bio', 'website', 'counts.media', 'counts.followers', 'counts.followed_by' ),
													'ssl'			=>	false
												),
						'foursquare'	=>	array(	'name'			=>	CBTxt::T( 'Foursquare' ),
													'field'			=>	'foursquare_userid',
													'icon'			=>	'foursquare',
													'button'		=>	'info',
													'profile'		=>	'https://foursquare.com/user/[id]',
													'permissions'	=>	array(),
													'fields'		=>	array(	'id', 'firstName', 'lastName', 'gender', 'relationship', 'canonicalUrl', 'friends.count', 'birthday', 'tips.count', 'homeCity', 'bio', 'contact.phone', 'contact.verifiedPhone',
																				'contact.email', 'contact.twitter', 'contact.facebook', 'photos.count', 'checkinPings', 'pings', 'type', 'mayorships.count', 'checkins.count', 'requests.count', 'lists.count',
																				'blockedStatus', 'createdAt', 'referralId', 'venue', 'scores.recent', 'scores.max', 'scores.checkinsCount', 'scores.goal'
																			),
													'ssl'			=>	false
												),
						'github'		=>	array(	'name'			=>	CBTxt::T( 'GitHub' ),
													'field'			=>	'github_userid',
													'icon'			=>	'github',
													'button'		=>	'default',
													'profile'		=>	null,
													'permissions'	=>	array(	'public_repo', 'repo', 'repo_deployment', 'repo:status', 'delete_repo', 'notifications', 'gist', 'read:repo_hook', 'write:repo_hook', 'admin:repo_hook', 'admin:org_hook', 'read:org',
																				'write:org', 'admin:org', 'read:public_key', 'write:public_key', 'admin:public_key'
																			),
													'fields'		=>	array(	'id', 'login', 'avatar_url', 'gravatar_id', 'url', 'html_url', 'followers_url', 'following_url', 'gists_url', 'starred_url', 'subscriptions_url', 'organizations_url', 'repos_url',
																				'events_url', 'received_events_url', 'type', 'site_admin', 'name', 'company', 'blog', 'location', 'email', 'hireable', 'bio', 'public_repos', 'public_gists', 'followers', 'following',
																				'created_at', 'updated_at', 'total_private_repos', 'owned_private_repos', 'private_gists', 'disk_usage', 'collaborators'
																			),
													'ssl'			=>	false
												),
						'vkontakte'		=>	array(	'name'			=>	CBTxt::T( 'VKontakte' ),
													'field'			=>	'vkontakte_userid',
													'icon'			=>	'vk',
													'button'		=>	'primary',
													'profile'		=>	'http://vk.com/id[id]',
													'permissions'	=>	array(	'notify', 'friends', 'photos', 'audio', 'video', 'docs', 'notes', 'pages', 'status', 'offers', 'questions', 'groups', 'notifications', 'stats', 'ads', 'offline' ),
													'fields'		=>	array(	'uid', 'first_name', 'last_name', 'deactivated', 'verified', 'blacklisted', 'sex', 'bdate', 'city.title', 'country.title', 'home_town', 'photo_50', 'photo_100', 'photo_200_orig', 'photo_200',
																				'photo_400_orig', 'photo_max', 'photo_max_orig', 'online', 'domain', 'has_mobile', 'contacts.mobile_phone', 'contacts.home_phone', 'site', 'status', 'last_seen.time', 'followers_count',
																				'common_count', 'counters.albums', 'counters.videos', 'counters.audios', 'counters.notes', 'counters.friends', 'counters.groups', 'counters.online_friends', 'counters.mutual_friends',
																				'counters.user_videos', 'counters.followers', 'counters.user_photos', 'counters.subscriptions', 'occupation.name', 'nickname', 'relation', 'personal.political', 'personal.langs',
																				'personal.religion', 'personal.inspired_by', 'personal.people_main', 'personal.life_main', 'personal.smoking', 'personal.alcohol', 'wall_comments', 'activities', 'interests', 'music',
																				'movies', 'tv', 'books', 'games', 'about', 'quotes', 'can_post', 'can_see_all_posts', 'can_see_audio', 'can_write_private_message', 'timezone', 'screen_name'
																			),
													'ssl'			=>	false
												),
						'steam'			=>	array(	'name'			=>	CBTxt::T( 'Steam' ),
													'field'			=>	'steam_userid',
													'icon'			=>	'steam',
													'button'		=>	'success',
													'profile'		=>	'http://steamcommunity.com/profiles/[id]',
													'permissions'	=>	array(),
													'fields'		=>	array(	'steamid', 'personaname', 'profileurl', 'avatar', 'avatarmedium', 'avatarfull', 'personastate', 'communityvisibilitystate', 'profilestate', 'lastlogoff', 'commentpermission', 'realname',
																				'primaryclanid', 'timecreated', 'gameid', 'gameserverip', 'gameextrainfo', 'cityid', 'loccountrycode', 'locstatecode', 'loccityid'
																			),
													'ssl'			=>	false
												),
						'reddit'		=>	array(	'name'			=>	CBTxt::T( 'Reddit' ),
													'field'			=>	'reddit_userid',
													'icon'			=>	'reddit-alien',
													'button'		=>	'default',
													'profile'		=>	null,
													'permissions'	=>	array(	'edit', 'flair', 'history', 'modconfig', 'modflair', 'modlog', 'modposts', 'modwiki', 'mysubreddits', 'privatemessages', 'read', 'report', 'save', 'submit', 'subscribe', 'vote', 'wikiedit', 'wikiread' ),
													'fields'		=>	array(	'id', 'name', 'created', 'hide_from_robots', 'is_suspended', 'created_utc', 'link_karma', 'comment_karma', 'over_18', 'is_gold', 'is_mod', 'gold_expiration', 'inbox_count', 'has_verified_email',
																				'gold_creddits', 'suspension_expiration_utc'
																			),
													'ssl'			=>	false
												),
						'twitch'		=>	array(	'name'			=>	CBTxt::T( 'Twitch' ),
													'field'			=>	'twitch_userid',
													'icon'			=>	'twitch',
													'button'		=>	'default',
													'profile'		=>	null,
													'permissions'	=>	array(	'user_blocks_edit', 'user_blocks_read', 'user_follows_edit', 'channel_read', 'channel_editor', 'channel_commercial', 'channel_stream', 'channel_subscriptions', 'user_subscriptions',
																				'channel_check_subscription', 'chat_login'
																			),
													'fields'		=>	array(	'_id', 'type', 'name', 'created_at', 'updated_at', '_links.self', 'logo', 'display_name', 'email', 'partnered', 'bio', 'notifications.push', 'notifications.email' ),
													'ssl'			=>	false
												),
						'stackexchange'	=>	array(	'name'			=>	CBTxt::T( 'Stack Exchange' ),
													'field'			=>	'stackexchange_userid',
													'icon'			=>	'stack-exchange',
													'button'		=>	'info',
													'profile'		=>	'http://stackoverflow.com/users/[id]',
													'permissions'	=>	array(	'read_inbox', 'no_expiry', 'write_access', 'private_info' ),
													'fields'		=>	array(	'user_id', 'display_name', 'accept_rate', 'account_id', 'age', 'badge_counts.bronze', 'badge_counts.silver', 'badge_counts.gold', 'creation_date', 'is_employee', 'last_access_date', 'last_modified_date',
																				'link', 'location', 'profile_image', 'reputation', 'reputation_change_day', 'reputation_change_month', 'reputation_change_quarter', 'reputation_change_week', 'reputation_change_year', 'timed_penalty_date',
																				'user_type', 'website_url'
																			),
													'ssl'			=>	false
												),
						'pinterest'		=>	array(	'name'			=>	CBTxt::T( 'Pinterest' ),
													'field'			=>	'pinterest_userid',
													'icon'			=>	'pinterest text-danger',
													'button'		=>	'default',
													'profile'		=>	null,
													'permissions'	=>	array(	'write_public', 'read_relationships', 'write_relationships' ),
													'fields'		=>	array(	'id', 'username', 'first_name', 'last_name', 'bio', 'created_at', 'counts.pins', 'counts.following', 'counts.followers', 'counts.boards', 'counts.likes' ),
													'ssl'			=>	true
												),
						'amazon'		=>	array(	'name'			=>	CBTxt::T( 'Amazon' ),
													'field'			=>	'amazon_userid',
													'icon'			=>	'amazon',
													'button'		=>	'warning',
													'profile'		=>	null,
													'permissions'	=>	array(	'postal_code' ),
													'fields'		=>	array(	'user_id', 'name', 'email', 'postal_code' ),
													'ssl'			=>	false
												),
						'yahoo'			=>	array(	'name'			=>	CBTxt::T( 'Yahoo' ),
													'field'			=>	'yahoo_userid',
													'icon'			=>	'yc-square',
													'button'		=>	'default',
													'profile'		=>	null,
													'permissions'	=>	array(),
													'fields'		=>	array(	'guid', 'ageCategory', 'birthYear', 'birthdate', 'created', 'displayAge', 'familyName', 'gender', 'givenName', 'image.height', 'image.width', 'image.size', 'image.imageUrl', 'lang', 'location', 'memberSince',
																				'nickname', 'profileUrl', 'timeZone', 'isConnected'
																			),
													'ssl'			=>	false
												),
						'paypal'		=>	array(	'name'			=>	CBTxt::T( 'PayPal' ),
													'field'			=>	'paypal_userid',
													'icon'			=>	'paypal',
													'button'		=>	'primary',
													'profile'		=>	null,
													'permissions'	=>	array(	'address', 'phone', 'https://uri.paypal.com/services/paypalattributes' ),
													'fields'		=>	array(	'user_id', 'sub', 'name', 'given_name', 'family_name', 'middle_name', 'picture', 'email', 'email_verified', 'gender', 'birthdate', 'zoneinfo', 'locale', 'phone_number', 'address.street_address', 'address.locality',
																				'address.region', 'address.postal_code', 'address.country', 'verified_account', 'account_type', 'age_range', 'payer_id'
																			),
													'ssl'			=>	false
												),
						'disqus'		=>	array(	'name'			=>	CBTxt::T( 'Disqus' ),
													'field'			=>	'disqus_userid',
													'icon'			=>	'disqus',
													'button'		=>	'info',
													'profile'		=>	null,
													'permissions'	=>	array(	'write', 'admin' ),
													'fields'		=>	array(	'id', 'username', 'name', 'email', 'isFollowing', 'disable3rdPartyTrackers', 'isPowerContributor', 'isFollowedBy', 'isPrimary', 'numFollowers', 'rep', 'numFollowing', 'numPosts', 'location', 'isPrivate', 'joinedAt',
																				'numLikesReceived', 'about', 'url', 'numForumsFollowing', 'profileUrl', 'reputation', 'avatar.permalink', 'signedUrl', 'isAnonymous'
																			),
													'ssl'			=>	false
												),
						'wordpress'		=>	array(	'name'			=>	CBTxt::T( 'WordPress' ),
													'field'			=>	'wordpress_userid',
													'icon'			=>	'wordpress text-primary',
													'button'		=>	'default',
													'profile'		=>	'https://en.gravatar.com/[id]',
													'permissions'	=>	array(),
													'fields'		=>	array(	'ID', 'display_name', 'username', 'email', 'primary_blog', 'primary_blog_url', 'language', 'token_site_id', 'token_scope', 'avatar_URL', 'profile_URL', 'verified', 'email_verified', 'date', 'site_count', 'visible_site_count',
																				'has_unseen_notes', 'newest_note_type', 'phone_account', 'is_valid_google_apps_country', 'logout_URL'
																			),
													'ssl'			=>	false
												),
						'meetup'		=>	array(	'name'			=>	CBTxt::T( 'Meetup' ),
													'field'			=>	'meetup_userid',
													'icon'			=>	'meetup text-danger',
													'button'		=>	'default',
													'profile'		=>	'http://www.meetup.com/members/[id]',
													'permissions'	=>	array(	'ageless', 'event_management', 'group_edit', 'group_content_edit', 'group_join_edit', 'messaging', 'profile_edit', 'reporting', 'rsvp' ),
													'fields'		=>	array(	'id', 'name', 'bio', 'birthday.day', 'birthday.month', 'birthday.year', 'country', 'city', 'state', 'gender', 'hometown', 'joined', 'lang', 'lat', 'lon', 'link', 'membership_count', 'messagable', 'messaging_pref', 'photo.highres_link',
																				'photo.photo_id', 'photo.photo_link', 'photo.thumb_link', 'reachable', 'visited'
																			),
													'ssl'			=>	false
												)
					);
	}

	/**
	 * Returns the provider callback url
	 *
	 * @deprecated Backend XML Only!
	 *
	 * @param string $name
	 * @return string
	 */
	public function getProviderCallback( $name )
	{
		global $_CB_framework;

		if ( ( ! $name ) || ( ! preg_match( '/^([a-zA-Z0-9]+)_/', $name, $matches ) ) ) {
			return null;
		}

		$providers		=	self::getProviders();
		$providerId		=	$matches[1];

		if ( ! isset( $providers[$providerId] ) ) {
			return null;
		}

		$liveSite		=	$_CB_framework->getCfg( 'live_site' );

		if ( $providers[$providerId]['ssl'] ) {
			$liveSite	=	str_replace( 'http://', 'https://', $liveSite );
		}

		return $liveSite . '/components/com_comprofiler/plugin/user/plug_cbconnect/endpoint.php?provider=' . urlencode( trim( strtolower( $providerId ) ) );
	}

	/**
	 * Returns an options array of provider permissions
	 *
	 * @deprecated Backend XML Only!
	 *
	 * @param string $name
	 * @return array
	 */
	public function getProviderPermissions( $name )
	{
		$options		=	array();

		if ( ( ! $name ) || ( ! preg_match( '/^([a-zA-Z0-9]+)_/', $name, $matches ) ) ) {
			return $options;
		}

		$providers		=	self::getProviders();
		$providerId		=	$matches[1];

		if ( ! isset( $providers[$providerId] ) ) {
			return $options;
		}

		$permissions	=	$providers[$providerId]['permissions'];

		foreach ( $permissions as $permission ) {
			$options[]	=	\moscomprofilerHTML::makeOption( $permission, $permission );
		}

		return $options;
	}

	/**
	 * Returns an options array of provider fields
	 *
	 * @deprecated Backend XML Only!
	 *
	 * @param string $name
	 * @param string $value
	 * @param string $controlName
	 * @return array
	 */
	public function getProviderFields( $name, $value, $controlName )
	{
		$options		=	array();

		if ( ( ! $controlName ) || ( ! preg_match( '/^params\[([a-zA-Z0-9]+)_/', $controlName, $matches ) ) ) {
			return $options;
		}

		$providers		=	self::getProviders();
		$providerId		=	$matches[1];

		if ( ! isset( $providers[$providerId] ) ) {
			return $options;
		}

		$fields			=	$providers[$providerId]['fields'];

		foreach ( $fields as $field ) {
			$options[]	=	\moscomprofilerHTML::makeOption( $field, $field );
		}

		return $options;
	}

	/**
	 * Returns a list of provider substitutions for username formatting
	 *
	 * @deprecated Backend XML Only!
	 *
	 * @param string $name
	 * @return string
	 */
	public function getProviderSubstitutions( $name )
	{
		$substitutions			=	array( '[provider]', '[profile_id]', '[profile_username]', '[profile_name]', '[profile_firstname]', '[profile_middlename]', '[profile_lastname]', '[profile_email]' );

		if ( ( ! $name ) || ( ! preg_match( '/^([a-zA-Z0-9]+)_/', $name, $matches ) ) ) {
			return implode( ', ', $substitutions );
		}

		$providers				=	self::getProviders();
		$providerId				=	$matches[1];

		if ( ! isset( $providers[$providerId] ) ) {
			return implode( ', ', $substitutions );
		}

		$fields					=	$providers[$providerId]['fields'];

		foreach ( $fields as $field ) {
			$substitutions[]	=	'[' . trim( strtolower( $providerId ) ) . '_' . trim( strtolower( str_replace( array( '.', '-' ), '_', $field ) ) ) . ']';
		}

		return implode( ', ', $substitutions );
	}
}
