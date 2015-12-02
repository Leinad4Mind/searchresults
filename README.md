Search Results
===========

Search Results logs the searchwords your visitors searched for. It will totalize words and show you first and last time your visitor searched for the words. A cron job will maximize the words in the archive.

[![Build Status](https://travis-ci.org/ForumHulp/searchresults.svg?branch=master)](https://travis-ci.org/ForumHulp/searchresults)

## Requirements
* phpBB 3.1-dev or higher
* PHP 5.3 or higher

## Installation
You can install this on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by doing the following:

1. Copy the entire contents of this repo to to `FORUM_DIRECTORY/ext/forumhulp/searchresults/`
2. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
3. Click Search Results => `Enable`.

Note: This extension is in development. Installation is only recommended for testing purposes and is not supported on live boards. This extension will be officially released following phpBB 3.1.0. Extension depends on two core changes.

## Uninstallation
Navigate in the ACP to `Customise -> Extension Management -> Extensions` and click Search Results => `Disable`.

To permanently uninstall, click `Delete Data` and then you can safely delete the `/ext/forumhulp/searchresults/` folder.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2014 - John Peskens (ForumHulp.com)