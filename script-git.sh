#!/bin/bash

# Set your username and password
username="tahirkaloo.3"
password="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"

# Prompt the user for a commit message
read -p "Enter the commit message: " commit_message

# Set the Git credentials using the provided username and password
git config credential.helper 'store --file ~/.git-credentials'
echo "https://$username:$password@github.com" > ~/.git-credentials

# Perform git add .
git add .

# Perform git commit
git commit -m "$commit_message"

# Perform git push origin main
git push origin main

# Remove the stored Git credentials
rm ~/.git-credentials

