<?php

/**
 * @file plugins/generic/reviewqualitycollector/tests/RqcCallTest.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RqcCallTest
 * @ingroup plugins_generic_reviewqualitycollector
 *
 * @brief Test API calls to a reviewqualitycollector.org-like test server.
 */

require_once('lib/pkp/tests/phpunit-bootstrap.php');

//require_mock_env('env2'); // Required for mock app locale.

import('lib.pkp.tests.DatabaseTestCase');
//import('lib.pkp.classes.core.PKPRouter');
import('plugins.generic.reviewqualitycollector.classes.RqcCall');

class RqcCallTest extends DatabaseTestCase {

	protected function getAffectedTables() {
		return array();  //'rqc_delayed_calls');
	}

	protected function setUp() {
		parent::setUp();
		$this->rqccall = new RqcCall();
		//PluginRegistry::loadCategory('generic', true, 0);
		//$this->rqcplugin = PluginRegistry::getPlugin('generic', 'rqcplugin');
	}

	public function testDoPost() {
		$response = $this->rqccall->do_post(1, "http://www.fu-berlin.de"); //, "nix");
		self::assertEquals("output", $response);
	}
}
?>
