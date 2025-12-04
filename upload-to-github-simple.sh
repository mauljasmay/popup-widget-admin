#!/bin/bash

# GitHub Upload Script
# Ganti variabel berikut:
GITHUB_USERNAME="mauljasmay"
REPO_NAME="popup-widget-admin"
# Set GITHUB_TOKEN as environment variable for security
# export GITHUB_TOKEN="your_token_here"

# Check if token is set
if [ -z "$GITHUB_TOKEN" ]; then
    echo "Error: GITHUB_TOKEN environment variable not set"
    echo "Set it with: export GITHUB_TOKEN=\"your_token_here\""
    exit 1
fi

# Buat repository via API
curl -X POST \
  -H "Authorization: token $GITHUB_TOKEN" \
  -H "Accept: application/vnd.github.v3+json" \
  https://api.github.com/user/repos \
  -d "{
    \"name\": \"$REPO_NAME\",
    \"description\": \"popup-widget-admin\",
    \"private\": false,
    \"auto_init\": false
  }"

# Tambahkan remote
git remote add origin https://$GITHUB_USERNAME:$GITHUB_TOKEN@github.com/$GITHUB_USERNAME/$REPO_NAME.git

# Push ke GitHub
git push -u origin master

echo "Repository berhasil diupload ke GitHub!"
echo "URL: https://github.com/mauljasmay/popup-widget-admin.git"