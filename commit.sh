#!/bin/bash

# Simple commit script for Laravel project

# Check if message is provided
if [ -z "$1" ]; then
  echo "Usage: ./commit.sh \"Your commit message\""
  exit 1
fi

COMMIT_MESSAGE="$1"

# Check if we're in a git repository
if [ ! -d ".git" ]; then
  echo "Initializing git repository..."
  git init
  git remote add origin https://github.com/mhdniyas/league.git
fi

# Add all files
echo "Adding files to git..."
git add .

# Commit with the provided message
echo "Committing changes with message: $COMMIT_MESSAGE"
git commit -m "$COMMIT_MESSAGE"

# Push to the remote repository
echo "Pushing to remote repository..."
git push -u origin main || git push -u origin master

echo "Changes committed and pushed successfully!"
