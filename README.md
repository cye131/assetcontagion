


#Changelog
X

## [1.6]
- Added Kendall's Tau and Spearman's Rho
- Added OLS univariate beta


## [1.5] - 2018-06-20
- Upgraded MySQL to 8.0
- Overhauled models
- Changed some InnoDB optimization featuers in my.cnf
- Added specs_categories to db for scalability in: frequency, trailing periods, different correlation measurements, and different data transformations (log, pchg, etc)
- Reworked other database tables and fixed broken foreign keys
- Overhauled API backend to be more usable without having to directly access a SQL GUI
- Removed primary natural keys from historical data tables and replaced them with a primary composite index
- Updated some dependencies via composer
