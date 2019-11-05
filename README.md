# Review Quality Collector (RQC) plugin for OJS
created 2017-08-30, Lutz Prechelt

Version 2019-11-05 
Status: **unfinished, not yet usable**

## What it is

Review Quality Collector (RQC) is an initiative for improving the quality of 
scientific peer review. 
Its core is a mechanism that supplies a reviewer with a receipt for 
their work for each journal year.
The receipt is based on grading each review according to a journal-specific
review quality definition.

This plugin is an OJS adapter for the RQC API, by which OJS
reports the reviewing data of individual article
submissions to RQC so that RQC can arrange the grading and add the
reviews to the respective reviewers' receipts.

Find the RQC API description at
https://reviewqualitycollector.org/t/api


## How it works

- extends the journal master data forms by two additional fields: 
  `rqcJournalId` and `rqcJournalKey`.
- When both are filled, they are checked against RQC 
  whether they are a valid pair.
- If they are accepted, they are stored as additional JournalSettings.
- If these settings exist, the plugin will add a button "RQC-grade the reviews"
  by which editors can submit the reviewing data for a given
  submission to RQC in order to trigger the grading.
  This step is optional for the editors.
  Depending on how RQC is configured for that journal, the given
  editor may then be redirected to RQC to perform (or not)
  a grading rightaway. 
- The plugin will also intercept the acceptance-decision-making
  event and send the decision and reviewing data for that submission
  to RQC then.
- Should the RQC service be unavailable when data is submitted
  automatically at decision time, the request will be stored
  and will be repeated once a day for several days until it goes through.
  

## How to use it: Installation

Target audience: OJS system administrators.

- The RQC plugin requires PHP 7. 
  It will not work with PHP 5.
  ([PHP 5 no longer receives security updates](https://secure.php.net/supported-versions.php))
- In `config.inc.php`, set `scheduled_tasks = On`.
- Make sure there is an entry in your OJS server's crontab
  (see "Scheduled Tasks" in the OJS `docs/README`) and that it includes
  RQC's `scheduldTasks.xml` besides the default one from `registry`.
  This could for instance look like this
  ```crontab
  0 * * * *	(cd /path/to/ojs; php tools/runScheduledTasks.php registry/scheduledTasks.xml plugins/generic/reviewqualitycollector/scheduledTasks.xml)
  ```
- Get the RQC plugin from within OJS.
  This only applies if RQC has already been updated to the version of OJS that you use.
  When a new OJS release appears, it may take some time to update
  the RQC plugin and get it accepted by the OJS team.
  Only then does it become available in the plugins list shown at
  `Settings->Website->Plugins->Plugin Gallery`.
  If _Review Quality Collector_ does not show up there,
  your OJS version is too new or too old.
  The earliest version of OJS for which RQC is available is OJS 3.2.?-?.
- Once installed, go to `Settings->Website->Plugins` and activate the
  plugin "Review Quality Collector (RQC)" in category Generic Plugins.


## How to use it: Journal setup

Target audience: OJS journal managers, RQC RQGuardians.

- Read about RQC at https://reviewqualitycollector.org.
- In RQC, register an account for yourself.
- Find your publisher in the RQC publisher directory (not yet implemented!!!)
  and ask your publisherpersons to create an RQC section for
  your journal. 
  (If needed, ask your publisher to register at RQC first.)
- Discuss review quality criteria with your co-editors.
  Formulate an RQC review quality definition file.
- As RQGuardian in RQC, set up your journal: 
  review quality definition,
  grading parameters (which people must/should/can/cannot grade a review).
- In OJS, open the RQC plugin settings and enter your
  public RQC journal ID and your secret RQC journal key.
  To do this, you need to be journal manager for your journal in OJS.
 

## How to use it: Daily use

- The best time to grade reviews in RQC is when you prepare
  the editorial decision in OJS.
- Therefore, each editor who is supposed to provide a grading
  should make it a habit to use the "RQC-grade reviews" button
  before entering their decision in OJS.
  This will redirect them into RQC for the grading and then
  back to OJS for entering the decision.
- If nobody does that (e.g. because editors are not supposed to
  perform review grading at your journal), OJS will submit the
  reviewing data to RQC when the editorial decision is entered into OJS.
  RQC will then remind graders via email.

  
---------------------------------------------------------------------------------------------------------------

## Development notes: Development setup

- OJS installation: https://pkp.sfu.ca/wiki/index.php?title=Github_Documentation_for_PKP_Contributors
- config.inc.php: 
  show_stacktrace = On 
  display_errors = Off
  activate_developer_functions = On
  rqc_server = http://192.168.3.1:8000
  (display_errors breaks Ajax functions when it kicks in)
- create journal rqctest:
  create users editor1, author1, reviewer1, reviewer2;
  create a submission, 2 review assignments, 2 reviews; 
  Settings->Website->Plugins turn on RQC plugin
- tests/backup.sh backup
  so you can quickly restore the review case during testing
- http://localhost:8000/index.php/rqctest/rqcdevhelper/
- Perhaps apply patches to OJS codebase from `ojspatches`.

For running OJS with RQC in development mode:
- The OJS VM has a host-only network on 192.168.3.1/24.  
  The VM is 192.168.3.6, the host is 192.168.3.1.
- For OJS to talk to RQC, start the Django dev server by
  `python manage.py runserver 0.0.0.0:8000`

(Outdated:) Branches:
- modifydecisionoptionshook (2 commits off rqcdev2: 6a275c7808 1a91b158af): 
  PR merged 2019-09 https://github.com/pkp/ojs/pull/2439 as 3c7a100610 in pkp-lib
- rqcdev2 has 4900 additional commits of very old history before the RQC commits
- rqcdev312l
- pkp-lib/robusthooks: make HookRegistry ignore non-callable callbacks:
  PR rejected https://github.com/pkp/pkp-lib/pull/5050


## Development notes: TO DO

- add the journal ID/key validation via an RQC call
- add all hooks and actual activity
- Switch to LetsEncrypt and put its root certificate into the plugin,
  because the Telekom certificate ends 2019-07-09
  (and RQC's ends 2019-03-27!)
- elaborate on "ask your publisher" in locale.xml
- write automated tests


### Development notes: RQC plugin

- Setting `activate_developer_functions = On` in `config.inc.php`
  enables `example_request` functionality in `RQCPlugin::manage`
  and `::getActions`. Not yet implemented.
- See
  [my PKP forum thread](https://forum.pkp.sfu.ca/t/need-help-to-build-review-quality-collector-rqc-plugin/33186/6)
- In particular regarding 
  [exploring the data model (qu. 5)](https://forum.pkp.sfu.ca/t/need-help-to-build-review-quality-collector-rqc-plugin/33186/9?u=prechelt)
- settings dialog does not close after OK.
- OJS review rounds must create successive submission ids for RQC.
- SpyHandler (now DevHelperHandler) gets 8 notices a la 
  "Undefined index: first_name in /home/vagrant/ojs/lib/pkp/classes/submission/PKPAuthorDAO.inc.php on line 127"
- Cronjob via PKPAcronPlugin?
- Delayed-call storage via Plugin::updateSchema and my own DAO?


## Development notes: RQC

- resubmit elsewhere counts as reject (or is its own decision?)
- do not submit confidential comments as part of the review.
- allow a file upload instead of review text???
- submit flag that RQC should emphasize the MHS page link,
  because grading-relevant material is only on the MHS page.


## Development notes: General OJS knowledge

- Many hooks are provided in `pkp-lib` like this 
  `HookRegistry::call(strtolower_codesafe(get_class($this) . '::validate')`
  (this particular one is from `classes/form/Form.inc.php`)
- DAO class names are in classes/core/Application.inc.php::getDAOmap())
- Forum: [create plugin and custom URL](https://forum.pkp.sfu.ca/t/ojs-3-0-3-0-1-browse-plugin-doesnt-show/26145/9?u=prechelt)
- Control flow, dispatch, URLs:
  https://pkp.sfu.ca/wiki/index.php?title=Router_Architecture
- see notes in 2018.3.txt of 2018-10-02
- Editor assignment: 
  "Can only recommend decision, authorized editor must record it."
- Settings->Website->Plugins->Plugin Gallery
- Plugins with Settings: 
  Google Analytics (Settings fail) 
  RQC (settings fail) 
  Web Feed (2 radiobuttons, one with an integer textbox) 
  Usage statistics (Sections, checkboxes, text fields, pulldown)
- Beware of the various _persistent_ caches, e.g. for plugin settings
- LoadHandler described in OSJ2.1 TechRef p. 46
- Maybe-helpful items from the code: 
  _callbackHandleCustomNavigationMenuItems 


## Development notes: OJS data model (the relevant parts)

RQC speaks of Journals, Submissions (i.e. articles), Authors, 
Reviewers, Reviews, Editors, EditorAssignments (which Editor has which
role for which Submission). 
Authors, Editors, and Reviewers are all Persons.

This is how these concepts are represented in OJS (class names,
other typical identifiers for such objects).
OJS speaks of four "stages": submission, review, copyediting, production.
RQC is concerned with the review stage only.
A revised article in OJS is not a new submission but rather a new
"review round" of the same submission (with new files).
Most classes have a corresponding DAO (data access object, as the ORM). 
Accessing objects often involves retrieving them (by using the DAO)
via the primary key, called the `id`:
- Journal: `Journal`; 
  the journal is often called the `context`.
- Submission: `Article` 
  (there is a `Submission` class as well: the PKP library's superclass).
- Person: `User` (extends `Identity`).
- Author: `Author` (but the term is oddly also used for the 'author' of 
  a Review: the Reviewer). 
  Inheritance: `Author<--PKPAuthor<--Identity<--DataObject`. 
- Editor: `User`(?) 
  Decision constants see `EditorDecisionActionsManager`. 
  Role ID constants see `Role`.
  The handling editor is called Section Editor in OJS. 
  `StageAssignment` appears to map a user ID to
  a stage (constants see `PKPApplication`, e.g. `WORKFLOW_STAGE_ID_EXTERNAL_REVIEW`)
  in a given role(?) (`UserGroup`(?), constants see ` `???).
- Reviewer: `User` (but usually called `reviewer`).
  - A `ReviewAssignment` connects a Reviewer to a Submission and also contains
    various timestamps, `declined`, `round`, `reviewRoundId`, `reviewMethod`. 
  - A `ReviewRound` represents the version number of a manuscript: 
    OJS could theoretically use the same `Article` for the, say,
    three versions of a manuscript until
    eventual acceptance or rejection and represents the versions explicitly.
    In contrast, RQC always uses three separate Submission 
    objects connected more implicitly via predecessor links.  
    How to get it: `ReviewRoundDAO::getLastReviewRoundBySubmissionId`
    (`ReviewRoundDAO::getCurrentRoundBySubmissionId` gets the round number).
  - Once the proper `ReviewRound` is known, get the `ReviewAssignments` by
    `ReviewAssignmentDAO::getByReviewRoundId` (one could also use 
    `ReviewAssignmentDAO::getBySubmissionId`).
    This returns an array. Its indices are the review IDs!.
- Review: `ReviewerSubmission`, but please hold on:
  - This class extends `Article`, presumably because reviewers can upload annotated 
    versions of the submission. 
  - Get one by `ReviewerSubmissionDAO::getReviewerSubmission($reviewId)`. ``
  - Attributes: timestamps, `declined`, `reviewMethod`, `reviewerId`, 
    `reviewId` (in fact reviewAssignmentId), `recommendation`, `decisions`.
  - Recommendation constants see 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_ACCEPT', 1); 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_PENDING_REVISIONS', 2); 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_RESUBMIT_HERE', 3); 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_RESUBMIT_ELSEWHERE', 4); 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_DECLINE', 5); 
    define('SUBMISSION_REVIEWER_RECOMMENDATION_SEE_COMMENTS', 6);
  - Also an array of `SubmissionComments` which represent
    review text. Retrieve by `getMostRecentPeerReviewComment`
  - `SubmissionComment` attributes: `authorEmail`, `authorId`, 
    `comments`, `commentTitle`, `commentType` (should be 1: `COMMENT_TYPE_PEER_REVIEW`),
    timestamps, `roleId`, `submissionId`, `viewable`.


### Development notes: phpunit and PKPTestCase/DatabaseTestCase

- Complete DB reset is available by returning `PKP_TEST_ENTIRE_DB`
  from `getAffectedTables`.
- Selenium test example see `StaticPagesFunctionalTest`. 

