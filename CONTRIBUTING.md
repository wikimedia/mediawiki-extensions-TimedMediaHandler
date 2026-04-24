# TimedMediaHandler Contributors Guide

## Welcome

Thank you for your interest in contributing to TimedMediaHandler.

This document is meant to help volunteer contributors understand where help is
most useful, what kinds of changes are likely to fit the project well, and how
to align contributions with the extension's current maintenance posture.

## Current support level

TimedMediaHandler is currently listed on mediawiki.org as having **Support
Level: Pending**.

In the Reader Growth maintenance model, this means the extension is still under
stewardship review. The Reader Growth team is the maintainer of last resort for
the time being, and the project should be approached as a maintenance-focused
codebase rather than one with active product development. The
[Developers/Maintainers](https://www.mediawiki.org/wiki/Developers/Maintainers)
page currently lists Brooke Vibber as the individual maintainer.

For contributors, that means:

- Bug fixes, regression fixes, documentation updates, tests, and compatibility
  work are especially helpful.
- Security, stability, and production reliability are higher priority than
  expanding the feature set.
- New feature work is not the default path while support is pending. Start with
  a task and discussion before investing in implementation.
- Review capacity may be limited. Conservative, well-tested patches are the
  easiest to evaluate and land.

For the current definition of support levels, see
[Reader Growth maintenance levels and responsibilities](https://www.mediawiki.org/wiki/Readers/Reader_Growth/Maintenance_Levels_and_Responsibilities).

## Project scope

TimedMediaHandler's role is to support audio and video handling in MediaWiki,
including:

- media metadata and format handling
- media embedding and playback
- timed text and subtitle support
- transcoding and derivative management
- maintenance and integration code needed to support those responsibilities

There is no published strategic goal to broaden the extension's remit at this
time. Contributions should therefore aim to keep the existing feature set
working well, keep the extension compatible with current MediaWiki and browser
expectations, and reduce maintenance risk over time.

Please avoid scope-expanding work unless there is clear maintainer agreement.
Examples include introducing new product surfaces, new long-term operational
obligations, or large new infrastructure dependencies.

## Feature areas in this repository

This repository does not currently include an `OWNERS.md` file. Until one
exists, treat the following as rough feature areas rather than strict ownership
boundaries:

- **Playback and embedding (backend)**
  `includes/TimedMediaHandler.php`,
  `includes/TimedMediaTransformOutput.php`,
  `includes/TimedMediaIframeOutput.php`, `includes/Hooks.php`
- **Playback and embedding (frontend)**
  `resources/ext.tmh.player*.{js,less}` — main player logic;
  `resources/videojs-resolution-switcher/` — quality switching UI;
  `resources/mw-subtitles-button/`, `resources/mw-info-button/`,
  `resources/mw-endcard/` — custom video.js components;
  `resources/lib/` — vendored third-party libraries (video.js)
- **Media format handlers and metadata**
  `includes/Handlers/`
- **Timed text and subtitle support**
  `includes/TimedText/`, `includes/ApiTimedText.php`,
  `includes/TimedTextPage.php`, `includes/SpecialOrphanedTimedText.php`
- **Transcoding, streaming, and status reporting**
  `includes/WebVideoTranscode/`, `includes/HLS/`,
  `includes/ApiTranscodeReset.php`, `includes/ApiTranscodeStatus.php`,
  `includes/SpecialTranscodeStatistics.php`, `maintenance/`, `sql/`
- **Integration and registration**
  `extension.json`, `includes/InstallerHooks.php`,
  `includes/RegistrationCallback.php`,
  `includes/MediaWikiEventIngress/`

If your change crosses several of these areas, or changes behavior visible to
site operators, start a discussion first.

## Contributions that are especially useful

The following kinds of work are a strong fit for the project's current support
level:

- fixing regressions in playback, uploads, timed text, thumbnailing, or
  transcoding
- improving test coverage for handler logic, timed text parsing, and transcode
  behavior
- updating code for MediaWiki core API changes and deprecations
- improving error handling, operational safety, and admin-facing diagnostics
- clarifying configuration, maintenance, or troubleshooting documentation
- making small, well-justified reliability or performance improvements that do
  not expand the extension's scope

## Contributions that should start with discussion

Please open a Phabricator task before implementing work like:

- new end-user features or large UX changes
- new codecs, container formats, or transcode profiles with operational cost
  implications
- changes that add new rights, API surfaces, database schema, or major
  configuration complexity
- player framework changes or large third-party library replacements
- changes that require new production infrastructure or increase ongoing
  maintenance burden

Because support is pending, proposals in these areas need explicit agreement on
scope and maintenance expectations.

## Reporting issues and proposing work

TimedMediaHandler uses Phabricator for task tracking. The mediawiki.org
extension page links to the
[`#timedmediahandler` project](https://phabricator.wikimedia.org/tag/timedmediahandler/).

When filing a task:

1. Search first to avoid duplicates.
2. Describe the problem, steps to reproduce, and expected behavior.
3. Include environment details when relevant, such as MediaWiki version,
   browser, codec/container details, and ffmpeg-related context.
4. If you are proposing new functionality, explain why it fits the current
   maintenance-first scope.

Small, low-risk fixes can still be proposed directly in Gerrit, but a linked
task is strongly preferred.

## Development and patch submission

For local setup and installation details, start with the [README](README.md).

TimedMediaHandler uses the standard MediaWiki Gerrit workflow:

1. Clone the repository from Gerrit.
2. Set up `git review -s`.
3. Make a focused change.
4. Run the relevant tests and linters.
5. Submit the patch with `git review`.

Before submitting, run at least:

```sh
# Linting and coding standards (run from the extension directory)
composer test
npm test
```

If you are changing substantial PHP logic, also consider running:

```sh
composer phan
```

Tests live under `tests/phpunit/`. If you change media handling, timed text, or
transcoding behavior, add or update tests whenever practical.

To run the PHPUnit test suite, from your MediaWiki core directory run:

```sh
composer phpunit -- extensions/TimedMediaHandler/tests/phpunit/
```


## General expectations

- Prefer small, reviewable patches over broad refactors.
- Keep changes within TimedMediaHandler's existing responsibilities unless
  maintainers agree otherwise.
- Document operator-facing behavior changes clearly.
- Preserve backward compatibility where practical, especially for deployed
  configuration and maintenance workflows.
- When in doubt, ask early on Phabricator rather than after a large patch is
  written.
