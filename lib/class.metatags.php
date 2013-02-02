<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Metatags generator
 *
 * @author		Blaschke, Markus <blaschke@teqneers.de>
 * @package 	tq_seo
 * @subpackage	lib
 * @version		$Id$
 */
class user_tqseo_metatags {

	/**
	 * List of stdWrap manipulations
	 * @var array
	 */
	protected $_stdWrapList = array();

	/**
	 * Add MetaTags
	 *
	 * @return	string			XHTML Code with metatags
	 */
	public function main() {
		global $TSFE;

		// INIT
		$ret			= array();
		$tsSetup		= $TSFE->tmpl->setup;
		$cObj			= $TSFE->cObj;
		$pageMeta		= array();
		$tsfePage		= $TSFE->page;

		$enableMetaDc	= true;

		if(!empty($tsSetup['plugin.']['tq_seo.']['metaTags.'])) {
			$tsSetupSeo = $tsSetup['plugin.']['tq_seo.']['metaTags.'];

			// get stdwrap list
			if( !empty($tsSetupSeo['stdWrap.']) ) {
				$this->_stdWrapList = $tsSetupSeo['stdWrap.'];
			}

			if( empty($tsSetupSeo['enableDC']) ) {
				$enableMetaDc = false;
			}

			#####################################
			# FETCH METADATA FROM PAGE
			#####################################
			// description
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['description_page'], $tsSetupSeo['conf.']['description_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['description'] = $tmp;
			}

			// keywords
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['keywords_page'], $tsSetupSeo['conf.']['keywords_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['keywords'] = $tmp;
			}

			// title
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['title_page'], $tsSetupSeo['conf.']['title_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['title'] = $tmp;
			}

			// author
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['author_page'], $tsSetupSeo['conf.']['author_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['author'] = $tmp;
			}

			// email
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['email_page'], $tsSetupSeo['conf.']['email_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['email'] = $tmp;
			}

			// last-update
			$tmp = $cObj->stdWrap( $tsSetupSeo['conf.']['lastUpdate_page'], $tsSetupSeo['conf.']['lastUpdate_page.'] );
			if( !empty($tmp) ) {
				$pageMeta['lastUpdate'] = $tmp;
			}

			// language
			if( !empty($tsSetupSeo['useDetectLanguage'])
				&& !empty( $tsSetup['config.']['language'] ) ) {
				$pageMeta['language'] = $tsSetup['config.']['language'];
			}

			// process page meta data
			foreach($pageMeta as $metaKey => $metaValue) {
				$metaValue = trim($metaValue);

				if( !empty($metaValue) ) {
					$tsSetupSeo[$metaKey] = $metaValue;
				}
			}

			#####################################
			# StdWrap List
			#####################################
			$stdWrapItemList = array(
				'title',
				'description',
				'keywords',
				'copyright',
				'language',
				'email',
				'author',
				'publisher',
				'distribution',
				'rating',
				'lastUpdate',
			);
			foreach($stdWrapItemList as $key) {
				$tsSetupSeo[$key] = $this->_applyStdWrap($key, $tsSetupSeo[$key]);
			}


			#####################################
			# PAGE META
			#####################################

			// title
			if( !empty($tsSetupSeo['title']) && $enableMetaDc ) {
				$ret[] = '<meta name="DC.title" content="'.htmlspecialchars($tsSetupSeo['title']).'" />';
			}

			// description
			if( !empty($tsSetupSeo['description']) ) {
				$ret[] = '<meta name="description" content="'.htmlspecialchars($tsSetupSeo['description']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.Description" content="'.htmlspecialchars($tsSetupSeo['description']).'" />';
				}
			}

			// keywords
			if( !empty($tsSetupSeo['keywords']) ) {
				$ret[] = '<meta name="keywords" content="'.htmlspecialchars($tsSetupSeo['keywords']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.Subject" content="'.htmlspecialchars($tsSetupSeo['keywords']).'" />';
				}
			}

			// copyright
			if( !empty($tsSetupSeo['copyright']) ) {
				$ret[] = '<meta name="copyright" content="'.htmlspecialchars($tsSetupSeo['copyright']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.Rights" content="'.htmlspecialchars($tsSetupSeo['copyright']).'" />';
				}
			}

			// language
			if( !empty($tsSetupSeo['language']) ) {
				$ret[] = '<meta http-equiv="content-language" content="'.htmlspecialchars($tsSetupSeo['language']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.Language" scheme="NISOZ39.50" content="'.htmlspecialchars($tsSetupSeo['language']).'" />';
				}
			}

			// email
			if( !empty($tsSetupSeo['email']) ) {
				$ret[] = '<link rev="made" href="mailto:'.htmlspecialchars($tsSetupSeo['email']).'" />';
				$ret[] = '<meta http-equiv="reply-to" content="'.htmlspecialchars($tsSetupSeo['email']).'" />';
			}

			// author
			if( !empty($tsSetupSeo['author']) ) {
				$ret[] = '<meta name="author" content="'.htmlspecialchars($tsSetupSeo['author']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.Creator" content="'.htmlspecialchars($tsSetupSeo['author']).'" />';
				}
			}

			// author
			if( !empty($tsSetupSeo['publisher']) && $enableMetaDc ) {
				$ret[] = '<meta name="DC.Publisher" content="'.htmlspecialchars($tsSetupSeo['publisher']).'" />';
			}

			// distribution
			if( !empty($tsSetupSeo['distribution']) ) {
				$ret[] = '<meta name="distribution" content="'.htmlspecialchars($tsSetupSeo['distribution']).'" />';
			}

			// rating
			if( !empty($tsSetupSeo['rating']) ) {
				$ret[] = '<meta name="rating" content="'.htmlspecialchars($tsSetupSeo['rating']).'" />';
			}

			// last-update
			if( !empty($tsSetupSeo['useLastUpdate']) && !empty($tsSetupSeo['lastUpdate']) ) {
				$ret[] = '<meta name="date" content="'.htmlspecialchars($tsSetupSeo['lastUpdate']).'" />';

				if($enableMetaDc) {
					$ret[] = '<meta name="DC.date" content="'.htmlspecialchars($tsSetupSeo['lastUpdate']).'" />';
				}
			}

			// expire
			if( !empty($tsSetupSeo['useLastUpdate']) && !empty($tsfePage['endtime']) ) {
				$ret[] = '<meta name="googlebot" content="unavailable_after: '.date('d-M-Y H:i:s T', $tsfePage['endtime']).'" /> ';
			}

			#####################################
			# CRAWLER ORDERS
			#####################################

			// robots
			$crawlerOrder = array();
			if( !empty($tsSetupSeo['robotsIndex']) && empty($tsfePage['tx_tqseo_is_exclude']) ) {
				$crawlerOrder['index'] = 'index';
			} else {
				$crawlerOrder['index'] = 'noindex';
			}

			if( !empty($tsSetupSeo['robotsFollow']) ) {
				$crawlerOrder['follow'] = 'follow';
			} else {
				$crawlerOrder['follow'] = 'nofollow';
			}

			if( empty($tsSetupSeo['robotsArchive']) ) {
				$crawlerOrder['archive'] = 'noarchive';
			}

			if( empty($tsSetupSeo['robotsSnippet']) ) {
				$crawlerOrder['snippet'] = 'nosnippet';
			}

			if( empty($tsSetupSeo['robotsOdp']) ) {
				$crawlerOrder['odp'] = 'noodp';
			}

			if( empty($tsSetupSeo['robotsYdir']) ) {
				$crawlerOrder['ydir'] = 'noydir';
			}

			$ret[] = '<meta name="robots" content="'.implode(',',$crawlerOrder).'" />';

			// revisit
			if( !empty($tsSetupSeo['revisit']) ) {
				$ret[] = '<meta name="revisit-after" content="'.htmlspecialchars($tsSetupSeo['revisit']).'" />';
			}

			#####################################
			# GEO POSITION
			#####################################

			// Geo-Position
			if( !empty($tsSetupSeo['geoPositionLatitude']) && !empty($tsSetupSeo['geoPositionLongitude']) ) {
				$ret[] = '<meta name="ICBM" content="'.htmlspecialchars($tsSetupSeo['geoPositionLatitude']).', '.htmlspecialchars($tsSetupSeo['geoPositionLongitude']).'" />';
				$ret[] = '<meta name="geo.position" content="'.htmlspecialchars($tsSetupSeo['geoPositionLatitude']).';'.htmlspecialchars($tsSetupSeo['geoPositionLongitude']).'" />';
			}

			// Geo-Region
			if( !empty($tsSetupSeo['geoRegion']) ) {
				$ret[] = '<meta name="geo.region" content="'.htmlspecialchars($tsSetupSeo['geoRegion']).'" />';
			}

			// Geo Placename
			if( !empty($tsSetupSeo['geoPlacename']) ) {
				$ret[] = '<meta name="geo.placename" content="'.htmlspecialchars($tsSetupSeo['geoPlacename']).'" />';
			}

			#####################################
			# MISC (Vendor specific)
			#####################################

			// Google Verification
			if( !empty($tsSetupSeo['googleVerification']) ) {
				$ret[] = '<meta name="google-site-verification" content="'.htmlspecialchars($tsSetupSeo['googleVerification']).'" />';
			}

			// MSN Verification
			if( !empty($tsSetupSeo['msnVerification']) ) {
				$ret[] = '<meta name="msvalidate.01" content="'.htmlspecialchars($tsSetupSeo['msnVerification']).'" />';
			}

			// Yahoo Verification
			if( !empty($tsSetupSeo['yahooVerification']) ) {
				$ret[] = '<meta name="y_key" content="'.htmlspecialchars($tsSetupSeo['yahooVerification']).'" />';
			}

			// WebOfTrust Verification
			if( !empty($tsSetupSeo['wotVerification']) ) {
				$ret[] = '<meta name="wot-verification" content="'.htmlspecialchars($tsSetupSeo['wotVerification']).'" />';
			}


			// PICS label
			if( !empty($tsSetupSeo['picsLabel']) ) {
				$ret[] = '<meta http-equiv="PICS-Label" content="'.htmlspecialchars($tsSetupSeo['picsLabel']).'" />';
			}

			#####################################
			# UserAgent
			#####################################

			// IE compatibility mode
			if( !empty($tsSetupSeo['ieCompatibilityMode']) ) {
				if( is_numeric($tsSetupSeo['ieCompatibilityMode']) ) {
					$ret[] = '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE'.(int)$tsSetupSeo['ieCompatibilityMode'].'" />';
				} else {
					$ret[] = '<meta http-equiv="X-UA-Compatible" content="'.htmlspecialchars($tsSetupSeo['ieCompatibilityMode']).'" />';
				}
			}

			#####################################
			# Link-Tags
			#####################################
			if( !empty($tsSetupSeo['linkGeneration']) ) {
				$rootLine = $TSFE->rootLine;
				ksort($rootLine);

				$currentPage	= end( $rootLine );

				$rootPage		= reset( $rootLine );
				$rootPageUrl	= NULL;
				if( !empty($rootPage) ) {
					$rootPageUrl	= $this->_generateLink($rootPage['uid']);
				}

				$upPage		= $currentPage['pid'];
				$upPageUrl	= NULL;
				if( !empty($upPage) ) {
					$upPageUrl		= $this->_generateLink($upPage);
				}

				$prevPage		= $TSFE->cObj->HMENU( $tsSetupSeo['sectionLinks.']['prev.'] );
				$prevPageUrl	= NULL;
				if( !empty($prevPage) ) {
					$prevPageUrl	= $this->_generateLink($prevPage);
				}

				$nextPage		= $TSFE->cObj->HMENU( $tsSetupSeo['sectionLinks.']['next.'] );
				$nextPageUrl	= NULL;
				if( !empty($nextPage) ) {
					$nextPageUrl	= $this->_generateLink($nextPage);
				}

				// Root (First page in rootline)
				if( !empty($rootPageUrl) ) {
					$ret[] = '<link rel="start" href="'.htmlspecialchars($rootPageUrl).'" />';
				}

				// Up (One page up in rootline)
				if( !empty($upPageUrl) ) {
					$ret[] = '<link rel="up" href="'.htmlspecialchars($upPageUrl).'" />';
				}

				// Next (Next page in rootline)
				if( !empty($nextPageUrl) ) {
					$ret[] = '<link rel="next" href="'.htmlspecialchars($nextPageUrl).'" />';
				}

				// Prev (Previous page in rootline)
				if( !empty($prevPageUrl) ) {
					$ret[] = '<link rel="prev" href="'.htmlspecialchars($prevPageUrl).'" />';
				}
			}

			// Canonical URL
			$canonicalUrl = null;

			if( !empty($tsfePage['tx_tqseo_canonicalurl']) ) {
				$canonicalUrl = $tsfePage['tx_tqseo_canonicalurl'];
			} elseif( !empty($tsSetupSeo['useCanonical']) ) {
				$strictMode = (bool)(int)$tsSetupSeo['useCanonical.']['strict'];
				$canonicalUrl = $this->_detectCanonicalPage($strictMode);
			}

			if( !empty($canonicalUrl) ) {
				$canonicalUrl = t3lib_div::locationHeaderUrl( $this->_generateLink($canonicalUrl) );

				if( !empty($canonicalUrl) ) {
					$ret[] = '<link rel="canonical" href="'.htmlspecialchars($canonicalUrl).'" />';
				}
			}

			#####################################
			# OTHERS (generated tags)
			#####################################
			// TODO
		}

		$separator = "\n	";

		return $separator.'<!-- MetaTags :: begin -->'.$separator.implode($separator, $ret).$separator.'<!-- MetaTags :: end -->'.$separator;
	}

	/**
	 * Generate a link via TYPO3-Api
	 *
	 * @return	string			URL
	 */
	protected function _generateLink($url, $conf = null) {
		global $TSFE;

		if( $conf === null ) {
			$conf = array();
		}

		$conf['parameter'] = $url;

		return $TSFE->cObj->typoLink_URL($conf);
	}

	/**
	 * Detect canonical page
	 *
	 * @return	string			Page Id or url
	 */
	protected function _detectCanonicalPage($strictMode = false) {
		global $TSFE;

		// Skip no_cache-pages
		if( !empty($TSFE->no_cache) ) {
			if( $strictMode ) {
				// force canonical-url to page url (without any parameters)
				return $TSFE->id;
			} else {
				return null;
			}
		}

		// Fetch chash
		$pageHash = NULL;
		if(!empty($TSFE->cHash)) {
			$pageHash = $TSFE->cHash;
		}

		if( !empty($this->cObj->data['content_from_pid']) ) {
			###############################
			# Content from pid
			###############################
			$ret = $this->cObj->data['content_from_pid'];
		} else {
			// Fetch pageUrl
			if( $pageHash !== NULL ) {
				$ret = $TSFE->anchorPrefix;
			} else {
				$ret = $TSFE->id;
			}
		}

		return $ret;
	}

	/**
	 * Process stdWrap from stdWrap list
	 *
	 * @param	string		$key	StdWrap-List key
	 * @param	string		$value	Value
	 * @return	string
	 */
	protected function _applyStdWrap($key, $value) {
		$key .= '.';
		if( empty($this->_stdWrapList[$key]) ) {
			return $value;
		}

		return $this->cObj->stdWrap($value, $this->_stdWrapList[$key]);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.metatags.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tq_seo/lib/class.metatags.php']);
}
?>