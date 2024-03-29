<?php

use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\Logger\LoggerFactory;

class SimpleCaptcha {
	protected static $messagePrefix = 'captcha-';

	/** @var boolean|null Was the CAPTCHA already passed and if yes, with which result? */
	private $captchaSolved = null;

	/**
	 * Used to select the right message.
	 * One of sendmail, createaccount, badlogin, edit, create, addurl.
	 * @var string
	 */
	protected $action;

	/** @var string Used in log messages. */
	protected $trigger;

	public function setAction( $action ) {
		$this->action = $action;
	}

	public function setTrigger( $trigger ) {
		$this->trigger = $trigger;
	}

	/**
	 * Return the error from the last passCaptcha* call.
	 * Not implemented but needed by some child classes.
	 * @return
	 */
	public function getError() {
		return null;
	}

	/**
	 * Returns an array with 'question' and 'answer' keys.
	 * Subclasses might use different structure.
	 * Since MW 1.27 all subclasses must implement this method.
	 * @return array
	 */
	function getCaptcha() {
		$a = mt_rand( 0, 100 );
		$b = mt_rand( 0, 10 );

		/* Minus sign is used in the question. UTF-8,
		   since the api uses text/plain, not text/html */
		$op = mt_rand( 0, 1 ) ? '+' : '−';

		// No space before and after $op, to ensure correct
		// directionality.
		$test = "$a$op$b";
		$answer = ( $op == '+' ) ? ( $a + $b ) : ( $a - $b );
		return [ 'question' => $test, 'answer' => $answer ];
	}

	function addCaptchaAPI( &$resultArr ) {
		$captcha = $this->getCaptcha();
		$index = $this->storeCaptcha( $captcha );
		$resultArr['captcha'] = $this->describeCaptchaType();
		$resultArr['captcha']['id'] = $index;
		$resultArr['captcha']['question'] = $captcha['question'];
	}

	/**
	 * Describes the captcha type for API clients.
	 * @return array An array with keys 'type' and 'mime', and possibly other
	 *   implementation-specific
	 */
	public function describeCaptchaType() {
		return [
			'type' => 'simple',
			'mime' => 'text/plain',
		];
	}

	/**
	 * Insert a captcha prompt into the edit form.
	 * This sample implementation generates a simple arithmetic operation;
	 * it would be easy to defeat by machine.
	 *
	 * Override this!
	 *
	 * @return string HTML
	 */
	function getForm( OutputPage $out, $tabIndex = 1 ) {
		$captcha = $this->getCaptcha();
		$index = $this->storeCaptcha( $captcha );

		return "<p><label for=\"wpCaptchaWord\">{$captcha['question']} = </label>" .
			Xml::element( 'input', [
				'name' => 'wpCaptchaWord',
				'class' => 'mw-ui-input',
				'id'   => 'wpCaptchaWord',
				'size'  => 5,
				'autocomplete' => 'off',
				'tabindex' => $tabIndex ] ) . // tab in before the edit textarea
			"</p>\n" .
			Xml::element( 'input', [
				'type'  => 'hidden',
				'name'  => 'wpCaptchaId',
				'id'    => 'wpCaptchaId',
				'value' => $index ] );
	}

	/**
	 * @param array $captchaData Data given by getCaptcha
	 * @param string $id ID given by storeCaptcha
	 * @return string Description of the captcha. Format is not specified; could be text, HTML, URL...
	 */
	public function getCaptchaInfo( $captchaData, $id ) {
		return $captchaData['question'] . ' =';
	}

	/**
	 * Show error message for missing or incorrect captcha on EditPage.
	 * @param EditPage $editPage
	 * @param OutputPage $out
	 */
	function showEditFormFields( &$editPage, &$out ) {
		$page = $editPage->getArticle()->getPage();
		if ( !isset( $page->ConfirmEdit_ActivateCaptcha ) ) {
			return;
		}

		if ( $this->action !== 'edit' ) {
			unset( $page->ConfirmEdit_ActivateCaptcha );
			$out->addWikiText( $this->getMessage( $this->action )->text() );
			$out->addHTML( $this->getForm( $out ) );
		}
	}

	/**
	 * Insert the captcha prompt into an edit form.
	 * @param EditPage $editPage
	 */
	function editShowCaptcha( $editPage ) {
		$context = $editPage->getArticle()->getContext();
		$page = $editPage->getArticle()->getPage();
		$out = $context->getOutput();
		if ( isset( $page->ConfirmEdit_ActivateCaptcha ) ||
			$this->shouldCheck( $page, '', '', $context )
		) {
			$out->addWikiText( $this->getMessage( $this->action )->text() );
			$out->addHTML( $this->getForm( $out ) );
		}
		unset( $page->ConfirmEdit_ActivateCaptcha );
	}

	/**
	 * Show a message asking the user to enter a captcha on edit
	 * The result will be treated as wiki text
	 *
	 * @param $action string Action being performed
	 * @return Message
	 */
	public function getMessage( $action ) {
		// one of captcha-edit, captcha-addurl, captcha-badlogin, captcha-createaccount,
		// captcha-create, captcha-sendemail
		$name = static::$messagePrefix . $action;
		$msg = wfMessage( $name );
		// obtain a more tailored message, if possible, otherwise, fall back to
		// the default for edits
		return $msg->isDisabled() ? wfMessage( static::$messagePrefix . 'edit' )  : $msg;
	}

	/**
	 * Inject whazawhoo
	 * @fixme if multiple thingies insert a header, could break
	 * @param $form HTMLForm
	 * @return bool true to keep running callbacks
	 */
	function injectEmailUser( &$form ) {
		global $wgCaptchaTriggers, $wgOut, $wgUser;
		if ( $wgCaptchaTriggers['sendemail'] ) {
			$this->action = 'sendemail';
			if ( $wgUser->isAllowed( 'skipcaptcha' ) ) {
				wfDebug( "ConfirmEdit: user group allows skipping captcha on email sending\n" );
				return true;
			}
			$form->addFooterText(
				"<div class='captcha'>" .
				$wgOut->parse( $this->getMessage( 'sendemail' )->text() ) .
				$this->getForm( $wgOut ) .
				"</div>\n" );
		}
		return true;
	}

	/**
	 * Inject whazawhoo
	 * @fixme if multiple thingies insert a header, could break
	 * @param QuickTemplate $template
	 * @return bool true to keep running callbacks
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	function injectUserCreate( &$template ) {
		global $wgCaptchaTriggers, $wgOut, $wgUser;
		if ( $wgCaptchaTriggers['createaccount'] ) {
			$this->action = 'createaccount';
			if ( $wgUser->isAllowed( 'skipcaptcha' ) ) {
				wfDebug( "ConfirmEdit: user group allows skipping captcha on account creation\n" );
				return true;
			}
			LoggerFactory::getInstance( 'authmanager' )->info( 'Captcha shown on account creation', [
				'event' => 'captcha.display',
				'type' => 'accountcreation',
			] );
			$captcha = "<div class='captcha'>" .
				$wgOut->parse( $this->getMessage( 'createaccount' )->text() ) .
				// FIXME: Hardcoded tab index
				// Usually, the CAPTCHA is added after the E-Mail address field,
				// which actually has 6 as the tabIndex, but
				// there may are wikis which allows to mention the "real name",
				// which would have 7 as tabIndex, so increase
				// 6 by 2 and use it for the CAPTCHA -> 8 (the submit button has a tabIndex of 10)
				$this->getForm( $wgOut, 8 ) .
				"</div>\n";
			// for older MediaWiki versions
			if ( is_callable( [ $template, 'extend' ] ) ) {
				$template->extend( 'extrafields', $captcha );
			} else {
				$template->set( 'header', $captcha );
			}
		}
		return true;
	}

	/**
	 * Inject a captcha into the user login form after a failed
	 * password attempt as a speedbump for mass attacks.
	 * @fixme if multiple thingies insert a header, could break
	 * @param $template QuickTemplate
	 * @return bool true to keep running callbacks
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	function injectUserLogin( &$template ) {
		$perUserTriggered = false;
		$username = $template->get( 'name', '' );
		if ( $username !== '' ) {
			// Note: The first time the user attempts to login, they may
			// get a incorrect password error due to the captcha not being
			// shown since we don't know that they will attempt to login in
			// to a captcha-limitted user account, until they actually try.
			$perUserTriggered = $this->isBadLoginPerUserTriggered( $username );
		}
		$perIPTriggered = $this->isBadLoginTriggered();
		if ( $perIPTriggered || $perUserTriggered ) {
			global $wgOut;

			LoggerFactory::getInstance( 'authmanager' )->info( 'Captcha shown on login', [
				'event' => 'captcha.display',
				'type' => 'login',
				'perIp' => $perIPTriggered,
				'perUser' => $perUserTriggered
			] );
			$this->action = 'badlogin';
			$captcha = "<div class='captcha'>" .
				$wgOut->parse( $this->getMessage( 'badlogin' )->text() ) .
				$this->getForm( $wgOut ) .
				"</div>\n";
			// for older MediaWiki versions
			if ( is_callable( [ $template, 'extend' ] ) ) {
				$template->extend( 'extrafields', $captcha );
			} else {
				$template->set( 'header', $captcha );
			}
		}
		return true;
	}

	/**
	 * When a bad login attempt is made, increment an expiring counter
	 * in the memcache cloud. Later checks for this may trigger a
	 * captcha display to prevent too many hits from the same place.
	 * @param User $user
	 * @param string $password
	 * @param int $retval authentication return value
	 * @return bool true to keep running callbacks
	 * @deprecated 1.27 pre-AuthManager hook handler
	 */
	function triggerUserLogin( $user, $password, $retval ) {
		if ( $retval === LoginForm::WRONG_PASS ) {
			$this->increaseBadLoginCounter( $user->getName() );
		} elseif ( $retval === LoginForm::SUCCESS ) {
			$this->resetBadLoginCounter( $user->getName() );
		}
		return true;
	}

	/**
	 * Increase bad login counter after a failed login.
	 * The user might be required to solve a captcha if the count is high.
	 * @param string $username
	 * TODO use Throttler
	 */
	public function increaseBadLoginCounter( $username ) {
		global $wgCaptchaTriggers, $wgCaptchaBadLoginExpiration,
			   $wgCaptchaBadLoginPerUserExpiration;
		$cache = ObjectCache::getLocalClusterInstance();

		if ( $wgCaptchaTriggers['badlogin'] ) {
			$key = $this->badLoginKey();
			$count = ObjectCache::getLocalClusterInstance()->get( $key );
			if ( !$count ) {
				$cache->add( $key, 0, $wgCaptchaBadLoginExpiration );
			}

			$cache->incr( $key );
		}

		if ( $wgCaptchaTriggers['badloginperuser'] && $username ) {
			$key = $this->badLoginPerUserKey( $username );
			$count = $cache->get( $key );
			if ( !$count ) {
				$cache->add( $key, 0, $wgCaptchaBadLoginPerUserExpiration );
			}

			$cache->incr( $key );
		}
	}

	/**
	 * Reset bad login counter after a successful login.
	 * @param string $username
	 */
	public function resetBadLoginCounter( $username ) {
		global $wgCaptchaTriggers;

		if ( $wgCaptchaTriggers['badloginperuser'] && $username ) {
			$cache = ObjectCache::getLocalClusterInstance();
			$cache->delete( $this->badLoginPerUserKey( $username ) );
		}
	}

	/**
	 * Check if a bad login has already been registered for this
	 * IP address. If so, require a captcha.
	 * @return bool
	 * @access private
	 */
	public function isBadLoginTriggered() {
		global $wgCaptchaTriggers, $wgCaptchaBadLoginAttempts;
		$cache = ObjectCache::getLocalClusterInstance();
		return $wgCaptchaTriggers['badlogin']
			&& (int)$cache->get( $this->badLoginKey() ) >= $wgCaptchaBadLoginAttempts;
	}

	/**
	 * Is the per-user captcha triggered?
	 *
	 * @param $u User|String User object, or name
	 * @return boolean|null False: no, null: no, but it will be triggered next time
	 */
	public function isBadLoginPerUserTriggered( $u ) {
		global $wgCaptchaTriggers, $wgCaptchaBadLoginPerUserAttempts;
		$cache = ObjectCache::getLocalClusterInstance();

		if ( is_object( $u ) ) {
			$u = $u->getName();
		}
		return $wgCaptchaTriggers['badloginperuser']
			&& (int)$cache->get( $this->badLoginPerUserKey( $u ) ) >= $wgCaptchaBadLoginPerUserAttempts;
	}

	/**
	 * Check if the current IP is allowed to skip captchas. This checks
	 * the whitelist from two sources.
	 *  1) From the server-side config array $wgCaptchaWhitelistIP
	 *  2) From the local [[MediaWiki:Captcha-ip-whitelist]] message
	 *
	 * @return bool true if whitelisted, false if not
	 */
	function isIPWhitelisted() {
		global $wgCaptchaWhitelistIP, $wgRequest;
		$ip = $wgRequest->getIP();

		if ( $wgCaptchaWhitelistIP ) {
			if ( IP::isInRanges( $ip, $wgCaptchaWhitelistIP ) ) {
				return true;
			}
		}

		$whitelistMsg = wfMessage( 'captcha-ip-whitelist' )->inContentLanguage();
		if ( !$whitelistMsg->isDisabled() ) {
			$whitelistedIPs = $this->getWikiIPWhitelist( $whitelistMsg );
			if ( IP::isInRanges( $ip, $whitelistedIPs ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the on-wiki IP whitelist stored in [[MediaWiki:Captcha-ip-whitelist]]
	 * page from cache if possible.
	 *
	 * @param Message $msg whitelist Message on wiki
	 * @return array whitelisted IP addresses or IP ranges, empty array if no whitelist
	 */
	private function getWikiIPWhitelist( Message $msg ) {
		$cache = ObjectCache::getMainWANInstance();
		$cacheKey = $cache->makeKey( 'confirmedit', 'ipwhitelist' );

		$cachedWhitelist = $cache->get( $cacheKey );
		if ( $cachedWhitelist === false ) {
			// Could not retrieve from cache so build the whitelist directly
			// from the wikipage
			$whitelist = $this->buildValidIPs(
				explode( "\n", $msg->plain() )
			);
			// And then store it in cache for one day. This cache is cleared on
			// modifications to the whitelist page.
			// @see ConfirmEditHooks::onPageContentSaveComplete()
			$cache->set( $cacheKey, $whitelist, 86400 );
		} else {
			// Whitelist from the cache
			$whitelist = $cachedWhitelist;
		}

		return $whitelist;
	}

	/**
	 * From a list of unvalidated input, get all the valid
	 * IP addresses and IP ranges from it.
	 *
	 * Note that only lines with just the IP address or IP range is considered
	 * as valid. Whitespace is allowed but if there is any other character on
	 * the line, it's not considered as a valid entry.
	 *
	 * @param string[] $input
	 * @return string[] of valid IP addresses and IP ranges
	 */
	private function buildValidIPs( array $input ) {
		// Remove whitespace and blank lines first
		$ips = array_map( 'trim', $input );
		$ips = array_filter( $ips );

		$validIPs = [];
		foreach ( $ips as $ip ) {
			if ( IP::isIPAddress( $ip ) ) {
				$validIPs[] = $ip;
			}
		}

		return $validIPs;
	}

	/**
	 * Internal cache key for badlogin checks.
	 * @return string
	 * @access private
	 */
	function badLoginKey() {
		global $wgRequest;
		$ip = $wgRequest->getIP();
		return wfGlobalCacheKey( 'captcha', 'badlogin', 'ip', $ip );
	}

	/*
	 * Cache key for badloginPerUser checks.
	 * @param $username string
	 * @return string
	 */
	private function badLoginPerUserKey( $username ) {
		$username = User::getCanonicalName( $username, 'usable' ) ?: $username;
		return wfGlobalCacheKey( 'captcha', 'badlogin', 'user', md5( $username ) );
	}

	/**
	 * Check if the submitted form matches the captcha session data provided
	 * by the plugin when the form was generated.
	 *
	 * Override this!
	 *
	 * @param string $answer
	 * @param array $info
	 * @return bool
	 */
	function keyMatch( $answer, $info ) {
		return $answer == $info['answer'];
	}

	// ----------------------------------

	/**
	 * @param Title $title
	 * @param string $action (edit/create/addurl...)
	 * @return bool true if action triggers captcha on $title's namespace
	 */
	function captchaTriggers( $title, $action ) {
		global $wgCaptchaTriggers, $wgCaptchaTriggersOnNamespace;
		// Special config for this NS?
		if ( isset( $wgCaptchaTriggersOnNamespace[$title->getNamespace()][$action] ) ) {
			return $wgCaptchaTriggersOnNamespace[$title->getNamespace()][$action];
	 }

		return ( !empty( $wgCaptchaTriggers[$action] ) ); // Default
	}

	/**
	 * @param WikiPage $page
	 * @param $content Content|string
	 * @param $section string
	 * @param IContextSource $context
	 * @param $oldtext string The content of the revision prior to $content.  When
	 *  null this will be loaded from the database.
	 * @return bool true if the captcha should run
	 */
	function shouldCheck( WikiPage $page, $content, $section, $context, $oldtext = null ) {
		// @codingStandardsIgnoreStart
		global $ceAllowConfirmedEmail;
		// @codingStandardsIgnoreEnd

		if ( !$context instanceof IContextSource ) {
			$context = RequestContext::getMain();
		}

		$request = $context->getRequest();
		$user = $context->getUser();

		// captcha check exceptions, which will return always false
		if ( $user->isAllowed( 'skipcaptcha' ) ) {
			wfDebug( "ConfirmEdit: user group allows skipping captcha\n" );
			return false;
		} elseif ( $this->isIPWhitelisted() ) {
			wfDebug( "ConfirmEdit: user IP is whitelisted" );
			return false;
		} elseif ( $ceAllowConfirmedEmail && $user->isEmailConfirmed() ) {
			wfDebug( "ConfirmEdit: user has confirmed mail, skipping captcha\n" );
			return false;
		}

		$title = $page->getTitle();
		$this->trigger = '';

		if ( $content instanceof Content ) {
			if ( $content->getModel() == CONTENT_MODEL_WIKITEXT ) {
				$newtext = $content->getNativeData();
			} else {
				$newtext = null;
			}
			$isEmpty = $content->isEmpty();
		} else {
			$newtext = $content;
			$isEmpty = $content === '';
		}

		if ( $this->captchaTriggers( $title, 'edit' ) ) {
			// Check on all edits
			$this->trigger = sprintf( "edit trigger by '%s' at [[%s]]",
				$user->getName(),
				$title->getPrefixedText() );
			$this->action = 'edit';
			wfDebug( "ConfirmEdit: checking all edits...\n" );
			return true;
		}

		if ( $this->captchaTriggers( $title, 'create' ) && !$title->exists() ) {
			// Check if creating a page
			$this->trigger = sprintf( "Create trigger by '%s' at [[%s]]",
				$user->getName(),
				$title->getPrefixedText() );
			$this->action = 'create';
			wfDebug( "ConfirmEdit: checking on page creation...\n" );
			return true;
		}

		// The following checks are expensive and should be done only,
		// if we can assume, that the edit will be saved
		if ( !$request->wasPosted() ) {
			wfDebug(
				"ConfirmEdit: request not posted, assuming that no content will be saved -> no CAPTCHA check"
			);
			return false;
		}

		if ( !$isEmpty && $this->captchaTriggers( $title, 'addurl' ) ) {
			// Only check edits that add URLs
			if ( $content instanceof Content ) {
				// Get links from the database
				$oldLinks = $this->getLinksFromTracker( $title );
				// Share a parse operation with Article::doEdit()
				$editInfo = $page->prepareContentForEdit( $content );
				if ( $editInfo->output ) {
					$newLinks = array_keys( $editInfo->output->getExternalLinks() );
				} else {
					$newLinks = [];
				}
			} else {
				// Get link changes in the slowest way known to man
				$oldtext = isset( $oldtext ) ? $oldtext : $this->loadText( $title, $section );
				$oldLinks = $this->findLinks( $title, $oldtext );
				$newLinks = $this->findLinks( $title, $newtext );
			}

			$unknownLinks = array_filter( $newLinks, [ &$this, 'filterLink' ] );
			$addedLinks = array_diff( $unknownLinks, $oldLinks );
			$numLinks = count( $addedLinks );

			if ( $numLinks > 0 ) {
				$this->trigger = sprintf( "%dx url trigger by '%s' at [[%s]]: %s",
					$numLinks,
					$user->getName(),
					$title->getPrefixedText(),
					implode( ", ", $addedLinks ) );
				$this->action = 'addurl';
				return true;
			}
		}

		global $wgCaptchaRegexes;
		if ( $newtext !== null && $wgCaptchaRegexes ) {
			if ( !is_array( $wgCaptchaRegexes ) ) {
				throw new UnexpectedValueException(
					'$wgCaptchaRegexes is required to be an array, ' . gettype( $wgCaptchaRegexes ) . ' given.'
				);
			}
			// Custom regex checks. Reuse $oldtext if set above.
			$oldtext = isset( $oldtext ) ? $oldtext : $this->loadText( $title, $section );

			foreach ( $wgCaptchaRegexes as $regex ) {
				$newMatches = [];
				if ( preg_match_all( $regex, $newtext, $newMatches ) ) {
					$oldMatches = [];
					preg_match_all( $regex, $oldtext, $oldMatches );

					$addedMatches = array_diff( $newMatches[0], $oldMatches[0] );

					$numHits = count( $addedMatches );
					if ( $numHits > 0 ) {
						$this->trigger = sprintf( "%dx %s at [[%s]]: %s",
							$numHits,
							$regex,
							$user->getName(),
							$title->getPrefixedText(),
							implode( ", ", $addedMatches ) );
						$this->action = 'edit';
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Filter callback function for URL whitelisting
	 * @param $url string to check
	 * @return bool true if unknown, false if whitelisted
	 * @access private
	 */
	function filterLink( $url ) {
		global $wgCaptchaWhitelist;
		static $regexes = null;

		if ( $regexes === null ) {
			$source = wfMessage( 'captcha-addurl-whitelist' )->inContentLanguage();

			$regexes = $source->isDisabled()
				? []
				: $this->buildRegexes( explode( "\n", $source->plain() ) );

			if ( $wgCaptchaWhitelist !== false ) {
				array_unshift( $regexes, $wgCaptchaWhitelist );
			}
		}

		foreach ( $regexes as $regex ) {
			if ( preg_match( $regex, $url ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Build regex from whitelist
	 * @param $lines string from [[MediaWiki:Captcha-addurl-whitelist]]
	 * @return array Regexes
	 * @access private
	 */
	function buildRegexes( $lines ) {
		# Code duplicated from the SpamBlacklist extension (r19197)
		# and later modified.

		# Strip comments and whitespace, then remove blanks
		$lines = array_filter( array_map( 'trim', preg_replace( '/#.*$/', '', $lines ) ) );

		# No lines, don't make a regex which will match everything
		if ( count( $lines ) == 0 ) {
			wfDebug( "No lines\n" );
			return [];
		} else {
			# Make regex
			# It's faster using the S modifier even though it will usually only be run once
			// $regex = 'https://+[a-z0-9_\-.]*(' . implode( '|', $lines ) . ')';
			// return '/' . str_replace( '/', '\/', preg_replace('|\\\*/|', '/', $regex) ) . '/Si';
			$regexes = [];
			$regexStart = [
				'normal' => '/^(?:https?:)?\/\/+[a-z0-9_\-.]*(?:',
				'noprotocol' => '/^(?:',
			];
			$regexEnd = [
				'normal' => ')/Si',
				'noprotocol' => ')/Si',
			];
			$regexMax = 4096;
			$build = [];
			foreach ( $lines as $line ) {
				# Extract flags from the line
				$options = [];
				if ( preg_match( '/^(.*?)\s*<([^<>]*)>$/', $line, $matches ) ) {
					if ( $matches[1] === '' ) {
						wfDebug( "Line with empty regex\n" );
						continue;
					}
					$line = $matches[1];
					$opts = preg_split( '/\s*\|\s*/', trim( $matches[2] ) );
					foreach ( $opts as $opt ) {
						$opt = strtolower( $opt );
						if ( $opt == 'noprotocol' ) {
							$options['noprotocol'] = true;
						}
					}
				}

				$key = isset( $options['noprotocol'] ) ? 'noprotocol' : 'normal';

				// FIXME: not very robust size check, but should work. :)
				if ( !isset( $build[$key] ) ) {
					$build[$key] = $line;
				} elseif ( strlen( $build[$key] ) + strlen( $line ) > $regexMax ) {
					$regexes[] = $regexStart[$key] .
						str_replace( '/', '\/', preg_replace( '|\\\*/|', '/', $build[$key] ) ) .
						$regexEnd[$key];
					$build[$key] = $line;
				} else {
					$build[$key] .= '|' . $line;
				}
			}
			foreach ( $build as $key => $value ) {
				$regexes[] = $regexStart[$key] .
					str_replace( '/', '\/', preg_replace( '|\\\*/|', '/', $build[$key] ) ) .
					$regexEnd[$key];
			}
			return $regexes;
		}
	}

	/**
	 * Load external links from the externallinks table
	 * @param $title Title
	 * @return Array
	 */
	function getLinksFromTracker( $title ) {
		$dbr = wfGetDB( DB_SLAVE );
		$id = $title->getArticleID(); // should be zero queries
		$res = $dbr->select( 'externallinks', [ 'el_to' ],
			[ 'el_from' => $id ], __METHOD__ );
		$links = [];
		foreach ( $res as $row ) {
			$links[] = $row->el_to;
		}
		return $links;
	}

	/**
	 * Backend function for confirmEdit() and confirmEditAPI()
	 * @param WikiPage $page
	 * @param $newtext string
	 * @param $section
	 * @param IContextSource $context
	 * @return bool false if the CAPTCHA is rejected, true otherwise
	 */
	private function doConfirmEdit( WikiPage $page, $newtext, $section, IContextSource $context ) {
		global $wgUser, $wgRequest;
		$request = $context->getRequest();

		// FIXME: Stop using wgRequest in other parts of ConfirmEdit so we can
		// stop having to duplicate code for it.
		if ( $request->getVal( 'captchaid' ) ) {
			$request->setVal( 'wpCaptchaId', $request->getVal( 'captchaid' ) );
			$wgRequest->setVal( 'wpCaptchaId', $request->getVal( 'captchaid' ) );
		}
		if ( $request->getVal( 'captchaword' ) ) {
			$request->setVal( 'wpCaptchaWord', $request->getVal( 'captchaword' ) );
			$wgRequest->setVal( 'wpCaptchaWord', $request->getVal( 'captchaword' ) );
		}
		if ( $this->shouldCheck( $page, $newtext, $section, $context ) ) {
			return $this->passCaptchaLimitedFromRequest( $wgRequest, $wgUser );
		} else {
			wfDebug( "ConfirmEdit: no need to show captcha.\n" );
			return true;
		}
	}

	/**
	 * An efficient edit filter callback based on the text after section merging
	 * @param RequestContext $context
	 * @param Content $content
	 * @param Status $status
	 * @param $summary
	 * @param $user
	 * @param $minorEdit
	 * @return bool
	 */
	function confirmEditMerged( $context, $content, $status, $summary, $user, $minorEdit ) {
		$legacyMode = !defined( 'MW_EDITFILTERMERGED_SUPPORTS_API' );
		if ( defined( 'MW_API' ) && $legacyMode ) {
			# API mode
			# The CAPTCHA was already checked and approved
			return true;
		}
		if ( !$context->canUseWikiPage() ) {
			// we check WikiPage only
			// try to get an appropriate title for this page
			$title = $context->getTitle();
			if ( $title instanceof Title ) {
				$title = $title->getFullText();
			} else {
				// otherwise it's an unknown page where this function is called from
				$title = 'unknown';
			}
			// log this error, it could be a problem in another extension,
			// edits should always have a WikiPage if
			// they go through EditFilterMergedContent.
			wfDebug( __METHOD__ . ': Skipped ConfirmEdit check: No WikiPage for title ' . $title );
			return true;
		}
		$page = $context->getWikiPage();
		if ( !$this->doConfirmEdit( $page, $content, false, $context ) ) {
			if ( $legacyMode ) {
				$status->fatal( 'hookaborted' );
			}
			$status->value = EditPage::AS_HOOK_ERROR_EXPECTED;
			$status->apiHookResult = [];
			// give an error message for the user to know, what goes wrong here.
			// this can't be done for addurl trigger, because this requires one "free" save
			// for the user, which we don't know, when he did it.
			if ( $this->action === 'edit' ) {
				$status->fatal(
					new RawMessage(
						Html::element(
							'div',
							[ 'class' => 'errorbox' ],
							$context->msg( 'captcha-edit-fail' )->text()
						)
					)
				);
			}
			$this->addCaptchaAPI( $status->apiHookResult );
			$page->ConfirmEdit_ActivateCaptcha = true;
			return $legacyMode;
		}
		return true;
	}

	function confirmEditAPI( $editPage, $newText, &$resultArr ) {
		$page = $editPage->getArticle()->getPage();
		if ( !$this->doConfirmEdit( $page, $newText, false, $editPage->getArticle()->getContext() ) ) {
			$this->addCaptchaAPI( $resultArr );
			return false;
		}

		return true;
	}

	/**
	 * Hook for user creation form submissions.
	 * @param User $u
	 * @param string $message
	 * @param Status $status
	 * @return bool true to continue, false to abort user creation
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	function confirmUserCreate( $u, &$message, &$status = null ) {
		global $wgUser, $wgRequest;

		if ( $this->needCreateAccountCaptcha() ) {
			$this->trigger = "new account '" . $u->getName() . "'";
			$success = $this->passCaptchaLimitedFromRequest( $wgRequest, $wgUser );
			LoggerFactory::getInstance(
				'authmanager'
			)->info( 'Captcha submitted on account creation', [
				'event' => 'captcha.submit',
				'type' => 'accountcreation',
				'successful' => $success,
			] );
			if ( !$success ) {
				// For older MediaWiki
				$message = wfMessage( 'captcha-createaccount-fail' )->text();
				// For MediaWiki 1.23+
				$status = Status::newGood();

				// Apply a *non*-fatal warning. This will still abort the
				// account creation but returns a "Warning" response to the
				// API or UI.
				$status->warning( 'captcha-createaccount-fail' );
				return false;
			}
		}
		return true;
	}

	/**
	 * Logic to check if we need to pass a captcha for the current user
	 * to create a new account, or not
	 *
	 * @param User $creatingUser
	 * @return bool true to show captcha, false to skip captcha
	 */
	public function needCreateAccountCaptcha( User $creatingUser = null ) {
		global $wgCaptchaTriggers, $wgUser;
		$creatingUser = $creatingUser ?: $wgUser;

		if ( $wgCaptchaTriggers['createaccount'] ) {
			if ( $creatingUser->isAllowed( 'skipcaptcha' ) ) {
				wfDebug( "ConfirmEdit: user group allows skipping captcha on account creation\n" );
				return false;
			}
			if ( $this->isIPWhitelisted() ) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Hook for user login form submissions.
	 * @param $u User
	 * @param $pass
	 * @param $retval
	 * @return bool true to continue, false to abort user creation
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	function confirmUserLogin( $u, $pass, &$retval ) {
		global $wgUser, $wgRequest;

		if ( $this->isBadLoginTriggered() || $this->isBadLoginPerUserTriggered( $u ) ) {
			if ( $this->isIPWhitelisted() ) {
				return true;
	  }

			$this->trigger = "post-badlogin login '" . $u->getName() . "'";
			$success = $this->passCaptchaLimitedFromRequest( $wgRequest, $wgUser );
			LoggerFactory::getInstance( 'authmanager' )->info( 'Captcha submitted on login', [
				'event' => 'captcha.submit',
				'type' => 'login',
				'successful' => $success,
			] );
			if ( !$success ) {
				// Emulate a bad-password return to confuse the shit out of attackers
				$retval = LoginForm::WRONG_PASS;
				return false;
			}
		}
		return true;
	}

	/**
	 * Check the captcha on Special:EmailUser
	 * @param $from MailAddress
	 * @param $to MailAddress
	 * @param $subject String
	 * @param $text String
	 * @param $error String reference
	 * @return Bool true to continue saving, false to abort and show a captcha form
	 */
	function confirmEmailUser( $from, $to, $subject, $text, &$error ) {
		global $wgCaptchaTriggers, $wgUser, $wgRequest;

		if ( $wgCaptchaTriggers['sendemail'] ) {
			if ( $wgUser->isAllowed( 'skipcaptcha' ) ) {
				wfDebug( "ConfirmEdit: user group allows skipping captcha on email sending\n" );
				return true;
			}
			if ( $this->isIPWhitelisted() ) {
				return true;
	  }

			if ( defined( 'MW_API' ) ) {
				# API mode
				# Asking for captchas in the API is really silly
				$error = wfMessage( 'captcha-disabledinapi' )->text();
				return false;
			}
			$this->trigger = "{$wgUser->getName()} sending email";
			if ( !$this->passCaptchaLimitedFromRequest( $wgRequest, $wgUser ) ) {
				$error = wfMessage( 'captcha-sendemail-fail' )->text();
				return false;
			}
		}
		return true;
	}

	/**
	 * @param $module ApiBase
	 * @return bool
	 */
	protected function isAPICaptchaModule( $module ) {
		return $module instanceof ApiEditPage || $module instanceof ApiCreateAccount;
	}

	/**
	 * @param $module ApiBase
	 * @param $params array
	 * @param $flags int
	 * @return bool
	 */
	public function APIGetAllowedParams( &$module, &$params, $flags ) {
		if ( $this->isAPICaptchaModule( $module ) ) {
			if ( defined( 'ApiBase::PARAM_HELP_MSG' ) ) {
				$params['captchaword'] = [
					ApiBase::PARAM_HELP_MSG => 'captcha-apihelp-param-captchaword',
				];
				$params['captchaid'] = [
					ApiBase::PARAM_HELP_MSG => 'captcha-apihelp-param-captchaid',
				];
			} else {
				// @todo: Remove this branch when support for MediaWiki < 1.25 is dropped
				$params['captchaword'] = null;
				$params['captchaid'] = null;
			}
		}

		return true;
	}

	/**
	 * @deprecated Since MediaWiki 1.25
	 * @param $module ApiBase
	 * @param $desc array
	 * @return bool
	 */
	public function APIGetParamDescription( &$module, &$desc ) {
		if ( $this->isAPICaptchaModule( $module ) ) {
			$desc['captchaid'] = 'CAPTCHA ID from previous request';
			$desc['captchaword'] = 'Answer to the CAPTCHA';
		}

		return true;
	}

	/**
	 * Checks, if the user reached the amount of false CAPTCHAs and give him some vacation
	 * or run self::passCaptcha() and clear counter if correct.
	 *
	 * @param WebRequest $request
	 * @param User $user
	 * @return bool
	 */
	public function passCaptchaLimitedFromRequest( WebRequest $request, User $user ) {
		list( $index, $word ) = $this->getCaptchaParamsFromRequest( $request );
		return $this->passCaptchaLimited( $index, $word, $user );
	}

	/**
	 * @param WebRequest $request
	 * @return array [ captcha ID, captcha solution ]
	 */
	protected function getCaptchaParamsFromRequest( WebRequest $request ) {
		$index = $request->getVal( 'wpCaptchaId' );
		$word = $request->getVal( 'wpCaptchaWord' );
		return [ $index, $word ];
	}

	/**
	 * Checks, if the user reached the amount of false CAPTCHAs and give him some vacation
	 * or run self::passCaptcha() and clear counter if correct.
	 *
	 * @param string $index Captcha idenitifier
	 * @param string $word Captcha solution
	 * @param User $user User for throttling captcha solving attempts
	 * @return bool
	 * @see self::passCaptcha()
	 */
	public function passCaptchaLimited( $index, $word, User $user ) {
		// don't increase pingLimiter here, just check, if CAPTCHA limit exceeded
		if ( $user->pingLimiter( 'badcaptcha', 0 ) ) {
			// for debugging add an proper error message, the user just see an false captcha error message
			$this->log( 'User reached RateLimit, preventing action.' );
			return false;
		}

		if ( $this->passCaptcha( $index, $word ) ) {
			return true;
		}

		// captcha was not solved: increase limit and return false
		$user->pingLimiter( 'badcaptcha' );
		return false;
	}

	/**
	 * Given a required captcha run, test form input for correct
	 * input on the open session.
	 * @param WebRequest $request
	 * @param User $user
	 * @return bool if passed, false if failed or new session
	 */
	public function passCaptchaFromRequest( WebRequest $request, User $user ) {
		list( $index, $word ) = $this->getCaptchaParamsFromRequest( $request );
		return $this->passCaptcha( $index, $word, $user );
	}

	/**
	 * Given a required captcha run, test form input for correct
	 * input on the open session.
	 * @param string $index Captcha idenitifier
	 * @param string $word Captcha solution
	 * @return bool if passed, false if failed or new session
	 */
	protected function passCaptcha( $index, $word ) {
		// Don't check the same CAPTCHA twice in one session,
		// if the CAPTCHA was already checked - Bug T94276
		if ( isset( $this->captchaSolved ) ) {
			return $this->captchaSolved;
		}

		$info = $this->retrieveCaptcha( $index );
		if ( $info ) {
			if ( $this->keyMatch( $word, $info ) ) {
				$this->log( "passed" );
				$this->clearCaptcha( $index );
				$this->captchaSolved = true;
				return true;
			} else {
				$this->clearCaptcha( $index );
				$this->log( "bad form input" );
				$this->captchaSolved = false;
				return false;
			}
		} else {
			$this->log( "new captcha session" );
			return false;
		}
	}

	/**
	 * Log the status and any triggering info for debugging or statistics
	 * @param string $message
	 */
	function log( $message ) {
		wfDebugLog( 'captcha', 'ConfirmEdit: ' . $message . '; ' .  $this->trigger );
	}

	/**
	 * Generate a captcha session ID and save the info in PHP's session storage.
	 * (Requires the user to have cookies enabled to get through the captcha.)
	 *
	 * A random ID is used so legit users can make edits in multiple tabs or
	 * windows without being unnecessarily hobbled by a serial order requirement.
	 * Pass the returned id value into the edit form as wpCaptchaId.
	 *
	 * @param array $info data to store
	 * @return string captcha ID key
	 */
	public function storeCaptcha( $info ) {
		if ( !isset( $info['index'] ) ) {
			// Assign random index if we're not udpating
			$info['index'] = strval( mt_rand() );
		}
		CaptchaStore::get()->store( $info['index'], $info );
		return $info['index'];
	}

	/**
	 * Fetch this session's captcha info.
	 * @param string $index
	 * @return array|false array of info, or false if missing
	 */
	public function retrieveCaptcha( $index ) {
		return CaptchaStore::get()->retrieve( $index );
	}

	/**
	 * Clear out existing captcha info from the session, to ensure
	 * it can't be reused.
	 */
	public function clearCaptcha( $index ) {
		CaptchaStore::get()->clear( $index );
	}

	/**
	 * Retrieve the current version of the page or section being edited...
	 * @param Title $title
	 * @param string $section
	 * @param integer $flags Flags for Revision loading methods
	 * @return string
	 * @access private
	 */
	function loadText( $title, $section, $flags = Revision::READ_LATEST ) {
		$rev = Revision::newFromTitle( $title, false, $flags );
		if ( is_null( $rev ) ) {
			return "";
		} else {
			$text = $rev->getText();
			if ( $section != '' ) {
				global $wgParser;
				return $wgParser->getSection( $text, $section );
			} else {
				return $text;
			}
		}
	}

	/**
	 * Extract a list of all recognized HTTP links in the text.
	 * @param $title Title
	 * @param $text string
	 * @return array of strings
	 */
	function findLinks( $title, $text ) {
		global $wgParser, $wgUser;

		$options = new ParserOptions();
		$text = $wgParser->preSaveTransform( $text, $title, $wgUser, $options );
		$out = $wgParser->parse( $text, $title, $options );

		return array_keys( $out->getExternalLinks() );
	}

	/**
	 * Show a page explaining what this wacky thing is.
	 */
	function showHelp() {
		global $wgOut;
		$wgOut->setPageTitle( wfMessage( 'captchahelp-title' )->text() );
		$wgOut->addWikiMsg( 'captchahelp-text' );
		if ( CaptchaStore::get()->cookiesNeeded() ) {
			$wgOut->addWikiMsg( 'captchahelp-cookies-needed' );
		}
	}

	/**
	 * Pass API captcha parameters on to the login form when using
	 * API account creation.
	 *
	 * @param ApiCreateAccount $apiModule
	 * @param LoginForm $loginForm
	 * @return hook return value
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	public function addNewAccountApiForm( $apiModule, $loginForm ) {
		global $wgRequest;
		$main = $apiModule->getMain();

		$id = $main->getVal( 'captchaid' );
		if ( $id ) {
			$wgRequest->setVal( 'wpCaptchaId', $id );

			// Suppress "unrecognized parameter" warning:
			$main->getVal( 'wpCaptchaId' );
		}

		$word = $main->getVal( 'captchaword' );
		if ( $word ) {
			$wgRequest->setVal( 'wpCaptchaWord', $word );

			// Suppress "unrecognized parameter" warning:
			$main->getVal( 'wpCaptchaWord' );
		}

		return true;
	}

	/**
	 * Pass extra data back in API results for account creation.
	 *
	 * @param ApiCreateAccount $apiModule
	 * @param LoginForm &loginPage
	 * @param array &$result
	 * @return bool: Hook return value
	 * @deprecated 1.27 pre-AuthManager logic
	 */
	public function addNewAccountApiResult( $apiModule, $loginPage, &$result ) {
		if ( $result['result'] !== 'Success' && $this->needCreateAccountCaptcha() ) {

			// If we failed a captcha, override the generic 'Warning' result string
			if ( $result['result'] === 'Warning' && isset( $result['warnings'] ) ) {
				$warnings = ApiResult::stripMetadataNonRecursive( $result['warnings'] );
				foreach ( $warnings as $warning ) {
					if ( $warning['message'] === 'captcha-createaccount-fail' ) {
						$this->addCaptchaAPI( $result );
						$result['result'] = 'NeedCaptcha';

						LoggerFactory::getInstance(
							'authmanager'
						)->info( 'Captcha data added in account creation API', [
							'event' => 'captcha.display',
							'type' => 'accountcreation',
						] );

						break;
					}
				}
			}
		}
		return true;
	}

	/**
	 * @return CaptchaAuthenticationRequest
	 */
	public function createAuthenticationRequest() {
		$captchaData = $this->getCaptcha();
		$id = $this->storeCaptcha( $captchaData );
		return new CaptchaAuthenticationRequest( $id, $captchaData );
	}

	/**
	 * Modify the apprearance of the captcha field
	 * @param AuthenticationRequest[] $requests
	 * @param array $fieldInfo Field description as given by AuthenticationRequest::mergeFieldInfo
	 * @param array $formDescriptor A form descriptor suitable for the HTMLForm constructor
	 * @param string $action One of the AuthManager::ACTION_* constants
	 */
	public function onAuthChangeFormFields(
		array $requests, array $fieldInfo, array &$formDescriptor, $action
	) {
		$req = AuthenticationRequest::getRequestByClass( $requests,
			CaptchaAuthenticationRequest::class );
		if ( !$req ) {
			return;
		}

		$formDescriptor['captchaWord'] = [
			'label-message' => null,
			'autocomplete' => false,
			'persistent' => false,
			'required' => true,
		] + $formDescriptor['captchaWord'];
	}
}
