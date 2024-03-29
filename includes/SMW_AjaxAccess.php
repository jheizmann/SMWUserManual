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

/*
 * Ajax functions for retrieving CSH articles for the Popup window.
 *
 * @file
 * @ingroup SMWUserManual
 */

/**
 * Enable use of AJAX features.
 */
$wgUseAjax = true;
$wgAjaxExportList[] = 'wfUmeAjaxGetArticleList';
$wgAjaxExportList[] = 'wfUmeAjaxGetArticleHtml';

/**
 * Gets a list of discource states and fetches a list of relevant articles from
 * the User Manual extension using an ask query.
 *
 * @access public
 * @param  string discourseState [followed by more params]
 * @global Object $smwgDefaultStore
 * @global Object $wgContLang
 * @return String html text
 */
function wfUmeAjaxGetArticleList(){
    global $smwgDefaultStore, $wgContLang;
    // be carefule what comes in the paramerers, accept text only
    $discourseStates = array();
    $params = func_get_args();
    while ($e = array_shift($params)) {
        if (preg_match('/^[\w ]+$/i', $e))
            $discourseStates[] = $e;
    }
    // if there are no parameters, return right away
    if (count($discourseStates) == 0)
        return json_encode(array('selection' => wfMsg('smw_ume_no_csh_articles')));

    // get all Csh articles for a certain discourse state
    $query = '[['.$wgContLang->getNsText(SMW_NS_USER_MANUAL).':+]]'
            .'[['.SMW_UME_PROPERTY_DISCOURSE_STATE.'::'.implode('||', $discourseStates).']]';
    // run the query now
	$fixparams = array(
            "format" => "list",
            "link" => "none",
	        "limit" => 500,
	);
    // set query output to html, so we can use it for the output directly
    $result = SMWQueryProcessor::getResultFromQueryString($query, $fixparams, array(), SMW_OUTPUT_WIKI);
    if (strlen($result) == 0)
        return json_encode(array('selection' => wfMsg('smw_ume_no_csh_articles')));
    $pages = explode(', '.$wgContLang->getNsText(SMW_NS_USER_MANUAL).':', $result);
    $result= wfMsg('smw_ume_select_topic').'<br/>'.
        '<select onchange="smwCsh.getPageContent(this[selectedIndex].text)" size="7" style="overflow:hidden">';
    for ($i = 0; $i < count($pages); $i++) {
        if ($i == 0) // the first values still has the namespace prefix
            $page = substr($pages[$i], strlen($wgContLang->getNsText(SMW_NS_USER_MANUAL)) + 1);
        else $page = $pages[$i];
        $result.='<option>'.htmlspecialchars($page).'</option>';
    }
    $result.='</select>';
    return json_encode(array('selection' => $result));
}

/**
 * Get the HTML of the wiki article in the User Manual Namespace
 *
 * @global Object $wgUser
 * @global Object $wgTitle
 * @global Object $wgParser
 * @param  String $page
 * @return String $html
 */
function wfUmeAjaxGetArticleHtml($page){
	global $wgUser, $wgTitle, $wgParser;
	// fetch MediaWiki page
	if (is_object($wgParser)) $psr =& $wgParser; else $psr = new Parser;
	$opt = ParserOptions::newFromUser($wgUser);
	$title = Title::newFromText($page, SMW_NS_USER_MANUAL);
    if (!$title) return wfMsg('smw_ume_no_help_article');
    $article = new Article($title);
    if (!$article) return wfMsg('smw_ume_no_help_article');
    // remove img tags but extract the content of the alt attribute and leave it in the text solemnly
    $wikitext= preg_replace('/\[\[Image:.*?alt=([^\]\|]*)[^\]]*\]\]/', '$1', $article->getRawText());
    // remove all remaining image tags that didn't have an alt parameter:
    $wikitext= preg_replace('/\[\[Image:[^\]]*\]\]/', '', $wikitext);
    // parse wikitext
	$out = $psr->parse($wikitext,$wgTitle,$opt,true,true);
    // initialize the result array
    $result = array();
	// fetch main HTML content of page
	$result['content'] = $out->getText();
    // add target blank to all links in the text
    $result['content'] = preg_replace('/(<a [^>]*)>/', '$1 target="_blank">', $result['content']);
    // set title of help page
    $result['title'] = '<img src="'.SMW_UME_PATH.'/skins/help.png" style="vertical-align:middle"/> '.$title->getText().'?';
    // fetch the link to further information from property
   	$pname = Title::newFromText(SMW_UME_PROPERTY_LINK, SMW_NS_PROPERTY);
    $prop = SMWPropertyValue::makeUserProperty($pname->getDBkey());
	$smwValues = smwfGetStore()->getPropertyValues($title, $prop);
	if (count($smwValues) > 0) {
        $umeLinks = $smwValues[0]->getDBkeys();
        $umeLink = str_replace(" ", "+", array_shift($umeLinks) );
   		if (strlen(trim($umeLink)) > 0)
            $result['link'] = '<a href="'.SMW_FORUM_URL.'?title='
                                .$umeLink.'" target="_blank">'
                                .wfMsg('smw_ume_link_to_smwforum').'</a>';
	}
	return json_encode($result);
}
