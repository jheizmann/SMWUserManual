<?php
/*
 * Copyright (C) Vulcan Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program.If not, see <http://www.gnu.org/licenses/>.
 *
 */


/**
 * SMWHalo context sensitive help extension
 *
 * @file
 * @author: Stephan Robotta
 * @ingroup SMWUserManual
 * @defgroup SMWUserManual SMWUserManual extension
 */

// no valid entry point for the extension
if ( !defined( 'MEDIAWIKI' ) ) die;

// Version number of the extension
define('SMW_USER_MANUAL_VERSION', '{{$VERSION}} [B{{$BUILDNUMBER}}]');

$wgExtensionCredits['other'][] = array(
    'name' => 'SMW User Manual',
    'version' => SMW_USER_MANUAL_VERSION,
    'author'=>"Maintained by [http://smwplus.com ontoprise GmbH].", 
    'url' => 'http://smwforum.ontoprise.com/smwforum/index.php/Help:Semantic_MediaWiki_User_Manual',
    'description' => 'A context sensitive help for SemanticMediaWiki, '.
        'and other semantic extensions. View online documentation in the '.
        '[http://smwforum.ontoprise.com/smwforum SMW+ User Forum].'
);

/**
 * User options start here, if you want to change the behavior of the
 * SMW UserManuals extensions, put the variable with the appropriate
 * value into your LocalSettings.php
 */ 

/**
 * Here you can define a custom namespace name for the context sensitive
 * help articles. This is done, so that no interference with any existing
 * articles might occur.
 * If you don't define anything, the default "UserManual" is used, which
 * is also language dependent. Do not use any existing namespace, this
 * will cause problems.
 */
$umegNamespace = null;

/**
 * Set here the namespace index from where the two additional namespaces will
 * be added to your wiki. If you don't set any value here, the next available
 * index will be used. The index number must be above 110. All below 100 is for
 * Mediawiki itself reserved and between 100 and 110 the Semantic MediaWiki uses
 * these values.
 */
$umegNamespaceIndex = null;

/**
 * When a help text pops up, the user can send any feedback or bugreports to the
 * SMW+ forum (http://smwforum.ontoprise.com) together with some information about
 * your wiki. All data is treated anonymously and there will be no chance to trace
 * it back to your wiki. We collect this information to know where the help is used
 * and what users think about the help text. Also bugreports can be send to SMW+.
 * If you not wish, that your wiki sends back any information to the SMW+ forum
 * please set this variable to false. This also makes sence, if you running a
 * cooperate wiki which has no access to the Internet.
 * All data that is send this way, will not be publicated on the SMW+ forum but
 * used internally by the SMW+ staff only.
 * The default value will be true, i.e. send user comments and bug reports to SMW+
 */
$umegSendFeedbackToSMWplus = true;

/**
 * Trigger here the comment function for the help articles. Feedback from the
 * users helps us to improve the content of the articles. When sending a rating 
 * and comments these will be stored in the SMW forum
 * (http://smwforum.ontoprise.com) and can be viewed by any visitor of the page.
 * Each comment is flagged with the IP address of the person submitting the comment
 * and rating. Also the time stamp is stored when the comment was send.
 * If you want your users to participate in the community, set the value to true.
 * The default is false, so that no comments will be send.
 */
$umegSendCommentsToSMWplus = false;

/**
 * If the extension is loaded a help link will be displayed. This is done always
 * even if there are no help articles in the wiki, the link is not much of a
 * help. Setting this variable to true will check if there articles and only
 * then the help link will be displayed. This requires a semantic query upon
 * each request. To avoid it, the default value is false.
 */
$umegCheckArticlesOnStartup = false;

/**
 * The popup will be displayed in a certain size. If the content doesn't fit
 * inside, scroll bars will be added to the content window. Define here the
 * width and height of the popup window. Values must be integers (unit will be
 * pixels)
 */
$umegPopupWidth = 400;
$umegPopupHeight = 460;

/**
 * Credentials for the SMW forum. This is neccessary to send ratings and feedback
 * from your wiki to the SMW Forum as well as sending bug reports. If you do not have
 * an account yet, please visit the SMW+ forum at:
 * http://smwforum.ontoprise.com/smwforum/
 * and sign up for an account.
 */
$umegSmwforumUser = '';
$umegSmwforumPass = '';

/* User options end here */

// internal variable to add a version number when the css and js files are retrieved
$umegStyleVersion= preg_replace('/[^\d]/', '', '{{$BUILDNUMBER}}' );
if (strlen($umegStyleVersion) > 0)
    $umegStyleVersion= '?'.$umegStyleVersion;

// SMWHalo is needed, check if it's installed and quit, if this is not the case
if (!defined("SMW_HALO_VERSION")) {
	trigger_error("SMWHalo extension is required but not installed.");
	die();
}

// define the SMW forum URL to the API to send rating and feedback to the smw forum.
define('SMW_FORUM_API', 'http://smwforum.ontoprise.com/smwforum/api.php');
// define the SMW forum URL to get a normal page (needed to fetch the CSH article list)
define('SMW_FORUM_URL', 'http://smwforum.ontoprise.com/smwforum/index.php');
// define the Bugzilla URL where new bugs can be submited
define('SMW_BUGZILLA_URL', 'http://smwforum.ontoprise.com/smwbugs/enter_bug.cgi');
// define the URL of the script that handles all requests send to the SMW+ Forum by feedback etc.
define('SMW_FORUM_COMMENT_URL', 'http://smwforum.ontoprise.com/smwforum/extensions/SMWUserManual/comment.php');
// Context sensitive help articles are fetched from the SMW forum by this query
define('SMW_FORUM_QUERY_CSH', '[[Category:Context sensitive help article]]');
// Porperty for discourse state
define('SMW_UME_PROPERTY_DISCOURSE_STATE', 'UME discourse state');
// Property for link to real help article
define('SMW_UME_PROPERTY_LINK', 'UME link');
// prefix for SMW forum template pages
define('SMW_TEMPLATE_PREFIX', 'Template');
// broken articles that sholuld be skiped
define('SMW_SKIP_CSH_ARTICLE', '');

// webserver path to extension
define('SMW_UME_PATH', $wgScriptPath.'/extensions/SMWUserManual');

// require additional files beloning to this extension
require_once(dirname(__FILE__).'/SMW_AjaxAccess.php');

/**
 * enable the user manual extension but here add the setup function to the
 * extension function hooks only. Once Mediawiki is completely setup, the
 * functions in this array will be launched which also sets up our extension
 * 
 * @global Array $wgExtensionFunctions
 */
function enableSMWUserManual() {
    global $wgExtensionFunctions;
    $wgExtensionFunctions[] = 'setupSMWUserManual';
    initSMWUserManualNamespaces();
}

/**
 * initializing of the SMW User Manual extension
 *
 * @global Array $wgExtraNamespaces
 * @global Array $smwgNamespacesWithSemanticLinks
 * @global int $umegNamespaceIndex
 * @global Object $umeLang
 * @global string $umegNamespace
 * @global Boolean $umegSendCommentsToSMWplus
 * @global Array $wgHooks
 */
function setupSMWUserManual() {
    global $umegCheckArticlesOnStartup, $wgHooks,
           $wgMessageCache, $wgLanguageCode, $umeLang;

    wfProfileIn('setupSMWUserManual');

    // MW API forwarder 
    require_once(dirname(__FILE__).'/SMW_MwApiForward.php');
    
    // add language messages to global message object
    $wgMessageCache->addMessages($umeLang->getTexts(), $wgLanguageCode);

    // check articles on startup? and add parser hook and javascript for help link
    if ($umegCheckArticlesOnStartup) {
        $res = umefGetHelpArticlePageCount();
        if ($res > 0)
            $wgHooks['BeforePageDisplay'][]='umefAddHtml2Page';
    }
    else
        $wgHooks['BeforePageDisplay'][]='umefAddHtml2Page';
        
    wfProfileOut('setupSMWUserManual');
}


/**
 * initializing of the SMW User Manual namespaces. This must be done before
 * the real setup that is called by the hook. Otherwise the Mediawiki API
 * doesn't know about the namespaces.
 *
 * @global Array $wgExtraNamespaces
 * @global Array $smwgNamespacesWithSemanticLinks
 * @global int $umegNamespaceIndex
 * @global Object $umeLang
 * @global string $umegNamespace
 */
function initSMWUserManualNamespaces() {
    global $wgExtraNamespaces, $smwgNamespacesWithSemanticLinks,
        $umegNamespaceIndex, $umeLang, $umegNamespace;

    wfProfileIn(__METHOD__);
    
    if ($umegNamespaceIndex == null) {
        $umegNamespaceIndex = 750;
    }
    // define the ns constants
    define('SMW_NS_USER_MANUAL', $umegNamespaceIndex);
    define('SMW_NS_USER_MANUAL_TALK', $umegNamespaceIndex + 1);

    // init language for name of the new ns
    umefInitLanguage();

    // add the custom ns to the extra namespace array
    if ($umegNamespace == null) {
        $umegNamespace = $umeLang->getNsText(SMW_NS_USER_MANUAL);
        $wgExtraNamespaces[SMW_NS_USER_MANUAL] = $umeLang->getNsText(SMW_NS_USER_MANUAL);
        $wgExtraNamespaces[SMW_NS_USER_MANUAL_TALK] = $umeLang->getNsText(SMW_NS_USER_MANUAL_TALK);
    }
    else {
        $wgExtraNamespaces[SMW_NS_USER_MANUAL] = $umegNamespace;
        $wgExtraNamespaces[SMW_NS_USER_MANUAL_TALK] = $umegNamespace.$umeLang->getTalkSuffix();
    }

    // add the normal UserManual ns to the semantic links array
    $smwgNamespacesWithSemanticLinks[SMW_NS_USER_MANUAL] = true;
    $smwgNamespacesWithSemanticLinks[SMW_NS_USER_MANUAL_TALK] = false;

    wfProfileOut(__METHOD__);
}

/**
 * add the javasript links to the html header of the page and add the help
 * link to the page content, so that the help link is displayed and the popup
 * with help message appears.
 */
function umefAddHtml2Page(&$out) {
    global $umegSendFeedbackToSMWplus, $umegSendCommentsToSMWplus,
           $umegPopupWidth, $umegPopupHeight, $umegNamespace, $umegSmwforumUser,
           $umegSmwforumPass, $umegStyleVersion;
    $out->addScript('
            <script type="text/javascript">/*<![CDATA[*/
                var DND_POPUP_DIR = "'.SMW_UME_PATH.'";
            /*]]>*/</script>
            <script type="text/javascript" src="'. SMW_UME_PATH . '/scripts/DndPopup.js'.$umegStyleVersion.'"></script>
            <script type="text/javascript" src="'. SMW_UME_PATH . '/scripts/smwCSH.js'.$umegStyleVersion.'"></script>
            <script type="text/javascript" src="'. SMW_UME_PATH . '/scripts/md5.js'.$umegStyleVersion.'"></script>
            <script type="text/javascript">/*<![CDATA[*/
                var umegNamespace = "'.$umegNamespace.'";
                var umegPopupWidth = '.$umegPopupWidth.';
                var umegPopupHeight = '.$umegPopupHeight.';
            /*]]>*/</script>
    ');

    if ($umegSendFeedbackToSMWplus) {
        $out->addScript('
            <script type="text/javascript">/*<![CDATA[*/
                var umegSmwForumUrl = "'.SMW_FORUM_URL.'";
                var umegSmwForumApi = "'.SMW_FORUM_API.'";
                var umegSmwForumCommentUrl = "'.SMW_FORUM_COMMENT_URL.'";
                var umegSmwBugzillaUrl = "'.SMW_BUGZILLA_URL.'";
                var umegSmwforumUser = "'.$umegSmwforumUser.'";
                var umegSmwforumPass = "'.$umegSmwforumPass.'";
                var umegSendCommentsToSMWplus = '.($umegSendCommentsToSMWplus ? 'true' : 'false').';
                var umegSMWplusVersion = "'.(defined('SMW_HALO_VERSION')?SMW_HALO_VERSION:'').'";
                var umegUMEVersion = "'.SMW_USER_MANUAL_VERSION.'";
            /*]]>*/</script>
        ');
    }
    $out->addStyle(SMW_UME_PATH . '/skins/usermanual_csh.css'.$umegStyleVersion, 'screen, projection' );

    $out->addHTML(umefDivBox().'
        <script type="text/javascript">/*<![CDATA[*/
        if ( typeof SMW_UserManual_CSH != "undefined" ) {
          var smwCsh = new SMW_UserManual_CSH("'.wfMsg('smw_ume_help_link').'");
          smwCsh.setHeadline("'.wfMsg('smw_ume_box_headline').'");
          smwCsh.setCloseLabel("'.wfMsg('smw_ume_close_label').'");
        }
        /*]]>*/</script>
    ');
    return true;
}

/**
 * init language for User Manual extension
 *
 * @global string $wgLanguageCode
 * @global SMW_UMLanguageEn $umeLang (or any other available language class)
 * @global Object $wgMessageCache
 */
function umefInitLanguage() {
    global $wgLanguageCode, $umeLang;
    wfProfileIn('umefInitLanguage');

    $className = 'SMW_UMLanguage'.str_replace('-', '_', ucfirst($wgLanguageCode));
    $langFile = dirname(__FILE__).'/../languages/'.$className.'.php';
    if (file_exists($langFile))
        require_once($langFile);
    else {
        require_once(dirname(__FILE__).'/../languages/SMW_UMLanguageEn.php');
        $className = 'SMW_UMLanguageEn';
    }

    $umeLang = new $className;

    wfProfileOut('umefInitLanguage');
}

/**
 * Check if ther are any help articles in the wiki
 *
 * @global string $umegNamespace
 * @return int number of articles
 */
function umefGetHelpArticlePageCount() {
    global $umegNamespace;

    wfProfileIn('umefGetHelpArticlePageCount');
    // check if there are CSH article, these must be in our
    // namsespace and the property UME disource state must be set
    $query = '[['.$umegNamespace.':+]][['.SMW_UME_PROPERTY_DISCOURSE_STATE.'::+]]';
	$fixparams = array( "format" => "count" );
    $result = SMWQueryProcessor::getResultFromQueryString($query, $fixparams, array(), SMW_OUTPUT_WIKI);
    wfProfileOut('umefGetHelpArticlePageCount');
    return intval($result);
}

function umefDivBox() {
    global $umegPopupWidth, $umegPopupHeight, $umegSendFeedbackToSMWplus;
    $closeImage = SMW_UME_PATH.'/skins/close.gif';
    $loadImage= SMW_UME_PATH.'/skins/load.gif';
    return '<div id="smw_csh_rendered_boxcontent" style="display:none;">
            <table class="cshRenderedBoxContentTable">
            <tr>
            <td class="cshTabSpacer">&nbsp;&nbsp;</td><td class="cshTabActive"'.(($umegSendFeedbackToSMWplus) ?' onclick="smwCsh.switchTab(this);"' : '').'>'.wfMsg('smw_ume_tab_help').'</td>
            '.(($umegSendFeedbackToSMWplus)
                ? '<td class="cshTabSpacer">&nbsp;&nbsp;</td><td class="cshTabInactive" onclick="smwCsh.switchTab(this);">'.wfMsg('smw_ume_tab_feedback').'</td>'
                : ''
            ).'
            <td class="cshTabSpacer" width="100%"></td>
            </tr>
            <tr><td colspan="5" class="cshTabCont">
            <span>
            <span class="cshHeadline">'.wfMsg('smw_ume_cpt_headline_1').'</span>
            <div id="smw_csh_selection">
            <span><img src="'.$loadImage.'" alt="load"/></span>
            </div>
            <div id="smw_csh_answer_head"></div>
            <div id="smw_csh_answer"></div>
            <div id="smw_csh_link_to_smw"></div>
            '.umefDivBoxRating().'
            </span>
            <span style="display:none">
            '.umefDivBoxFeedback().'
            </span>
            </td></tr></table></div>';
}
function umefDivBoxRating() {
    global $umegSendFeedbackToSMWplus, $umegSendCommentsToSMWplus, $umegSmwforumUser, $umegSmwforumPass;
    if (!$umegSendCommentsToSMWplus || strlen($umegSmwforumUser) == 0 || strlen($umegSmwforumPass) == 0) return '';
    $imgPath = SMW_UME_PATH.'/skins/';
    $ie = preg_match('/MSIE \d\.\d/', $_SERVER['HTTP_USER_AGENT']);
    return '<div id="smw_csh_rating" style="display: none"><img src="'.$imgPath.'right.png"/>'.wfMsg('smw_ume_did_it_help').
            (($ie) ? '<span onclick="smwCsh.openRatingBox()">' : '').
            '<input type="radio" name="smw_csh_did_it_help" value="1" onchange="smwCsh.openRatingBox()"/>'.wfMsg('smw_ume_yes').'
            <input type="radio" name="smw_csh_did_it_help" value="0" onchange="smwCsh.openRatingBox()"/>'.wfMsg('smw_ume_no').
            (($ie) ? '</span>' : '').
            '<div id="smw_csh_rating_box" style="display:none">
            <hr/>'.wfMsg('smw_ume_tip_rating').'<br/>
            <textarea rows="3"></textarea><br/>
            <span class="cshFootnote">'.wfMsg('smw_ume_tip_rating_note').'</span><br/>
            <input class="cshSubmitButton" type="submit" value="'.wfMsg('smw_ume_submit_feedback').'" onclick="smwCsh.sendRating()">
            <input type="submit" value="'.wfMsg('smw_ume_reset').'" onclick="smwCsh.resetRating()">
            </div>
            </div>
    ';
}

function umefDivBoxFeedback() {
    global $umegSendFeedbackToSMWplus, $umegSmwforumUser, $umegSmwforumPass;
    $imgPath = SMW_UME_PATH.'/skins/';
    if (!$umegSendFeedbackToSMWplus) return '';
    $html = '<div id="smw_csh_feedback">
            <span class="cshHeadline">'.wfMsg('smw_ume_cpt_headline_2').'</span>
            '.wfMsg('smw_ume_cpt_headline_2_1').'
            <table class="cshFeedbackFrame">
            <tr onclick="smwCsh.openCommentBox(this)"><td width="100%">
            <img src="'.$imgPath.'right.png"/>
            <span class="cshFeedbackBoxHeader">'.wfMsg('smw_ume_ask_your_own_q').'</span>
            <br/>'.wfMsg('smw_ume_ask_your_own_q_n').'
            </td><td>
            <img src="'.$imgPath.'question.png" align="right"/>
            </td></tr></table>
            <hr class="cshSpacerLine"/>
            '.wfMsg('smw_ume_cpt_headline_2_2').'
            <table class="cshFeedbackFrame">
            <tr onclick="smwCsh.openCommentBox(this)"><td width="100%">
            <img src="'.$imgPath.'right.png"/>
            <span class="cshFeedbackBoxHeader">'.wfMsg('smw_ume_add_comment').'</span>
            <br/>'.wfMsg('smw_ume_add_comment_n').'
            </td><td>
            <img src="'.$imgPath.'comment.png" align="right"/>
            </td></tr></table>';
    if (strlen($umegSmwforumUser) > 0 && strlen($umegSmwforumPass) > 0)
        $html .= '<table class="cshFeedbackFrame">
            <tr onclick="smwCsh.openCommentBox(this)"><td width="100%">
            <img src="'.$imgPath.'right.png"/>
            <span class="cshFeedbackBoxHeader">'.wfMsg('smw_ume_bug_discovered').'</span>
            <br/>'.wfMsg('smw_ume_bug_discovered_n').'
            </td><td>
            <img src="'.$imgPath.'bug.png" align="right"/>
            </td></tr></table>';
    $html.='</div>';
    return $html;
}
