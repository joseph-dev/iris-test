## Technical task for IRIS

#### Required steps to run the script

- Create a MySql database and provide proper credential to the database in file 'config/app.php'.
- Import file 'storage/database.sql' to create needed database structure.
- Configure path to file with transaction and mapping in file 'config/app.php'. 
  By default, it's configured to use file 'storage/report.csv' and mapping is correct for this file.
- Run command 'php index.php' from the root folder of the application.
- Before running the script for the second time, please remove all records in the database, 
  otherwise, trying to import transactions that have been imported before will cause errors.