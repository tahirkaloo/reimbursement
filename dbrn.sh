#!/bin/bash

# Database credentials
DB_USER="tahir"
DB_PASSWORD="11559933tk"

# Backup the database
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" mileage_form > mileage_form_backup.sql

# Export the database structure and data
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" mileage_form > mileage_form_export.sql

# Drop the 'mileage_form' database
mysql -u "$DB_USER" -p"$DB_PASSWORD" -e "DROP DATABASE mileage_form"

# Create the 'mileage_reimbursement' database
mysql -u "$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE mileage_reimbursement"

# Import the exported data into 'mileage_reimbursement' database
mysql -u "$DB_USER" -p"$DB_PASSWORD" mileage_reimbursement < mileage_form_export.sql

# Clean up the exported files
rm mileage_form_backup.sql mileage_form_export.sql

echo "Database 'mileage_form' has been renamed to 'mileage_reimbursement' successfully."

