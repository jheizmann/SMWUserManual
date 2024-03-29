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
 * Language file for the SMW User Manual extension
 *
 * @file
 * @ingroup SMWUserManual
 */

/**
 * Class for the english messages of the SMWUserManual extension
 *
 * @ingroup SMWUserManual
 */
class SMW_UMLanguageEn {

	protected $umeContentMessages = array(
        'smw_ume_help_link'       => 'Help',
        'smw_ume_box_headline'    => 'Help and Feedback',
        'smw_ume_tab_help'        => 'Help',
        'smw_ume_tab_feedback'    => 'Feedback',
        'smw_ume_cpt_headline_1'  => 'Context sensitive help',
        'smw_ume_cpt_headline_2'  => 'Choose feedback type',
	    'smw_ume_cpt_headline_2_1'=> 'Context sensitive help',
	    'smw_ume_cpt_headline_2_2'=> 'SMW+',
        'smw_ume_select_topic'    => 'Select help topic:',
        'smw_ume_link_to_smwforum'=> '&gt;&gt; Visit SMW<sup>+</sup> Userforum for more help',
        'smw_ume_did_it_help'     => 'Was this help content useful?',
        'smw_ume_ask_your_own_q'  => 'Your question is not listed in the help?',
	    'smw_ume_ask_your_own_q_n'=> 'Ask your own question to the SMW+ team.',
        'smw_ume_add_comment'     => 'Leave feedback to the software.',
	    'smw_ume_add_comment_n'   => '(e.g. positive/negative feedback, feature requests...)',
        'smw_ume_bug_discovered'  => 'Found a bug?',
	    'smw_ume_bug_discovered_n'=> 'Report it.',
        'smw_ume_submit_feedback' => 'Submit feedback',
        'smw_ume_reset'           => 'Reset',
        'smw_ume_yes'             => 'yes',
        'smw_ume_no'              => 'no',
        'smw_ume_close_label'     => 'Click here to close the UserManualExtension',
        'smw_ume_tip_rating'      => '<i><b>Thanks for your rating!</b><br/>Press the submit button to send your feedback.</i>',
	    'smw_ume_tip_rating_note' => 'If you like you can also provide a feedback to your rating.',
        // all these texts are for install only
        'smw_ume_create_page'     => 'Create wiki page: %s',
        'smw_ume_warning_page'    => 'Warning, page already exists',
	    'smw_ume_overwrite_page'  => 'Pages exists already, overwriting it',
        'smw_ume_del_csh_pages'   => 'Delete existing CSH pages from your local wiki...',
        'smw_ume_get_csh_pages'   => 'Fetch new CSH pages from the SMW Forum and install these in this wiki',
	    'smw_ume_export_csh_pages'=> 'Export CSH pages from the SMW Forum into a file',
	    'smw_ume_import_csh_pages'=> 'Import CSH pages from a file into this wiki',
        'smw_ume_no_article_list' => 'Could not get CSH article list from SMW forum',
        'smw_ume_install_done'    => 'Installation finished.',
	    'smw_ume_deinstall'       => 'Deinstall User Manual extension',
	    'smw_ume_nofopen'         => 'Could not open file for exporting/importing Help articles',
	    'smw_ume_delete_page'     => 'Delete wiki page: %s',
        'smw_ume_done'            => 'Done.',
        'smw_ume_error_code'      => 'Error code:',
        'smw_ume_no_csh_articles' => 'No matching help articles found for current state.',
        'smw_ume_no_help_article' => 'Error: Could not fetch help article',
	    'smw_ume_failed'          => 'Error.',
	    'smw_ume_missing_fparam'  => 'Error: parameter with file name missing.',
	    'smw_ume_import_fmissing' => 'Error: file for article import does not exist.',
	    'smw_ume_export_fexists'  => 'Error: file for article export exists already, use overwrite or change file name',
	    'smw_ume_setup_usage'     => "Usage: php UME_setup.php [-deio [--file=<file_name>] ]\nSee README file for further details.\n",
    );

    protected $umeNamespaces = array(
		SMW_NS_USER_MANUAL       => 'UserManual',
		SMW_NS_USER_MANUAL_TALK  => 'UserManual_talk',
	);

	protected $umeNamespaceAliases = array(
		'UserManual'       => SMW_NS_USER_MANUAL,
		'UserManual_talk'  => SMW_NS_USER_MANUAL_TALK
	);
    
    protected $umeTalkSuffix = '_talk';

    function getTexts() {
        return $this->umeContentMessages;
    }

    function getNsText($ns) {
        if (isset($this->umeNamespaces[$ns]))
            return $this->umeNamespaces[$ns];
        global $wgLang;
        if ($wgLang) return $wgLang->getNsText($ns);
    }

    function getTalkSuffix() {
        return $this->umeTalkSuffix;
    }
}
