#!/bin/bash

# Script to manage user roles and perform basic user management tasks

# Database configuration
DB_HOST="localhost"
DB_USER="tahir"
DB_PASS="11559933tk"
DB_NAME="user_accounts"

# Add role column to users table if it doesn't exist
add_role_column() {
  mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "ALTER TABLE users ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'user';"
  echo "Role column added to users table"
}

# Assign admin role to a user
assign_admin_role() {
  user_id="$1"
  mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "UPDATE users SET role = 'admin' WHERE user_id = $user_id;"
  echo "Admin role assigned to user $user_id"
}

# Revoke admin role from a user
revoke_admin_role() {
  user_id="$1"
  mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "UPDATE users SET role = 'user' WHERE user_id = $user_id;"
  echo "Admin role revoked from user $user_id"
}

# List all users
list_users() {
  mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SELECT * FROM users;"
}

# Manage user roles
manage_user_roles() {
  echo "1. List all users"
  echo "2. Assign admin role to a user"
  echo "3. Revoke admin role from a user"
  echo "0. Exit"

  read -p "Enter your choice: " choice
  case $choice in
    1)
      list_users
      ;;
    2)
      read -p "Enter the user ID: " user_id
      assign_admin_role "$user_id"
      ;;
    3)
      read -p "Enter the user ID: " user_id
      revoke_admin_role "$user_id"
      ;;
    0)
      echo "Exiting..."
      exit
      ;;
    *)
      echo "Invalid choice. Please try again."
      ;;
  esac

  manage_user_roles
}

# Uncomment the desired function calls based on your requirements

# Usage: ./user_management.sh add_role_column
# Uncomment the following line to add the role column to the users table
add_role_column

# Usage: ./user_management.sh assign_admin_role <user_id>
# Uncomment the following line to assign admin role to a user
# assign_admin_role "$2"

# Usage: ./user_management.sh revoke_admin_role <user_id>
# Uncomment the following line to revoke admin role from a user
# revoke_admin_role "$2"

# Usage: ./user_management.sh manage_user_roles
# Uncomment the following line to manage user roles interactively
manage_user_roles

