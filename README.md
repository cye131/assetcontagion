


##Changelog
## [1.7]
- Added OLS univariate beta


### [1.6]
- Added alternate correlation calculations (kendall's tau and spearman's rho)
- Added front-end selection for correlation type and frequency
- Changed router so that POST variables override $fromRouter variables on identical keys ($fromRouter now functions as a sort of "default")

### [1.5] - 2018-06-20
- Upgraded MySQL to 8.0
- Overhauled models
- Changed some InnoDB optimization featuers in my.cnf
- Added specs_categories to db for scalability in: frequency, trailing periods, different correlation measurements, and different data transformations (log, pchg, etc)
- Reworked other database tables and fixed broken foreign keys
- Overhauled API backend to be more usable without having to directly access a SQL GUI
- Removed primary natural keys from historical data tables and replaced them with a primary composite index
- Updated some dependencies via composer
