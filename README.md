


#Changelog

## [1.6]
- Added Kendall's Tau and Spearman's Rho
- Added OLS univariate beta


## [1.5] - 2018-06-20
- Upgraded MySQL to 8.0
- Overhauled models
- Changed my.cnf and some optimization features
- Reworked database system
- Added specs_categories to db for scalability in: frequency, trailing periods, different correlation measurements, and different data transformations (log, pchg, etc)
- Overhauled API backend to be more usable without having to directly access a SQL GUI
- Removed primary natural keys from historical data tables and replaced them with a primary composite index
- Updated some dependencies via composer
