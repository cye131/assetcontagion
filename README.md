


## ChangeLog
### [v1.10] - 2018-07-10


### [1.9] - 2018-07-06
- Added calculation for maximal information coefficient (MIC) and other maximal information nonparametric measures with Python & corresponding PHP router
- Added cron scripting for MIC calculations
- Added cron scripting for automating historical statistic calculations
- Added cleaner callback system for managing AJAX calls in regions.js
- Added play, back, forward, rewind and fast forward buttons on heatmap
- Added local custom installation of bootstrap.js and bootstrap.min.css
- Added rscripts and pythonscripts directories
- Added single state storage onload and turned the prior 2 AJAX call group wrappers to promises to improve execution speed of regions.js
- Added system to track AJAX exec time and fill in remaining time up to 500ms with a delay to even out the updating times
- Improved state management to UI interaction on regions.js
- Improved styling and spacing on average cross-regional equity correlation graph
- Moved Python virtualenv to project root
- Removed window variables from region.js


### [1.8] - 2018-07-03
- Added a correlation index, including database, CRUD model, Highcharts graph, script for cronjob, and REST API
- Added better coloration system to heatmap
- Added JS minification & concatenation system
- Added /js/ folder
- Added /cronscripts/ folder
- Improved all links by changing all absolute links to relative links
- Improved routing system to be inline with the arima project
- Improved and refactored all JS code
- Fixed get_hist_correl_by_date model with proper input reception


### [1.7] - 2018-06-23
- Forced chart reflow on new tab select
- Bug fix for historical heatmap with null obs_end
- Changed historical heatmap to increase by Monday-indexed dates (Previously did a count over the historical data table but was too slow)

### [1.6] - 2018-06-22
- Added alternate correlation calculations (kendall's tau and spearman's rho)
- Added front-end selection for correlation type and frequency
- Changed router so that POST variables override $fromRouter variables on identical keys ($fromRouter now functions as a default)

### [1.5] - 2018-06-20
- Upgraded MySQL to 8.0
- Overhauled models
- Changed some InnoDB optimization featuers in my.cnf
- Added specs_categories to db for scalability in: frequency, trailing periods, different correlation measurements, and different data transformations (log, pchg, etc)
- Reworked other database tables and fixed broken foreign keys
- Overhauled API backend to be more usable without having to directly access a SQL GUI
- Removed primary natural keys from historical data tables and replaced them with a primary composite index
- Updated some dependencies via composer
