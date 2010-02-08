<?php

# Scriptmanager needed for Halo extensions
include_once('extensions/ScriptManager/SM_Initialize.php');

#Import SMW, SMWHalo
include_once('extensions/SemanticMediaWiki/includes/SMW_Settings.php');
enableSemantics('http://wiki', true);

include_once('extensions/SMWHalo/includes/SMW_Initialize.php');
enableSMWHalo('SMWHaloStore2');

# Parser functions (needed for copyright template)
include_once('extensions/ParserFunctions/ParserFunctions.php');

#UserManual extension
require_once('extensions/SMWUserManual/includes/SMW_UserManual.php');
enableSMWUserManual();