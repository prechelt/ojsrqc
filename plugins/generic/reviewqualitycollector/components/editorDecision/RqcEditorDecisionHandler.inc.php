<?php

/**
 * @file plugins/generic/reviewqualitycollector/components/editorDecision/RqcEditorDecisionHandler.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RqcEditorDecisionHandler
 * @ingroup plugins_generic_reviewqualitycollector
 *
 * @brief Handle modal dialog before submitting and redirecting to RQC.
 */

import('classes.handler.Handler');

class RqcEditorDecisionHandler extends PKPHandler
{

	function __construct()
	{
		parent::__construct();
		$this->plugin = PluginRegistry::getPlugin('generic', 'rqcplugin');
	}

	/**
	 * Confirm redirection to RQC.
	 */
	function rqcGrade($args, $request)
	{
		//----- prepare processing:
		$router = $request->getRouter();
		$requestArgs = $request->getQueryArray();
		$context = $request->getContext();
		$journal = $router->getContext($request);
		$submissionId = $requestArgs['submissionId'];
		//----- modal dialog:
		return new JSONMessage(true, "<h1>Hello, Lutz!</h1>");  // TODO
	}
}
