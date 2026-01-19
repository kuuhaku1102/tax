#!/bin/bash

# Manual Rollback Script for WordPress Theme
# Usage: ./rollback.sh [backup_name]
# If no backup_name is provided, rolls back to the latest backup

set -e

# Configuration
THEME_DIR="/public_html/elmolinitos.com/wp-content/themes/tax-matching-theme"
BACKUP_DIR="/public_html/elmolinitos.com/wp-content/themes/backups"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== WordPress Theme Rollback Script ===${NC}"
echo ""

# Check if backup directory exists
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${RED}✗ Backup directory not found: $BACKUP_DIR${NC}"
    exit 1
fi

# List available backups
echo "Available backups:"
ls -lt $BACKUP_DIR | grep "^d" | awk '{print $9}' | nl
echo ""

# Determine which backup to restore
if [ -z "$1" ]; then
    # No argument provided, use latest backup
    BACKUP_TO_RESTORE=$(ls -t $BACKUP_DIR | head -n 1)
    echo -e "${YELLOW}No backup specified, using latest: $BACKUP_TO_RESTORE${NC}"
else
    # Use provided backup name
    BACKUP_TO_RESTORE="$1"
    if [ ! -d "$BACKUP_DIR/$BACKUP_TO_RESTORE" ]; then
        echo -e "${RED}✗ Backup not found: $BACKUP_TO_RESTORE${NC}"
        exit 1
    fi
    echo -e "${YELLOW}Using specified backup: $BACKUP_TO_RESTORE${NC}"
fi

echo ""
echo -e "${YELLOW}This will replace the current theme with backup: $BACKUP_TO_RESTORE${NC}"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo -e "${RED}Rollback cancelled${NC}"
    exit 0
fi

echo ""
echo "Starting rollback..."

# Create a backup of current state before rollback
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
EMERGENCY_BACKUP="$BACKUP_DIR/emergency_backup_$TIMESTAMP"

if [ -d "$THEME_DIR" ]; then
    echo "Creating emergency backup of current state..."
    cp -r $THEME_DIR $EMERGENCY_BACKUP
    echo -e "${GREEN}✓ Emergency backup created: $EMERGENCY_BACKUP${NC}"
fi

# Perform rollback
echo "Removing current theme..."
rm -rf $THEME_DIR

echo "Restoring backup..."
cp -r $BACKUP_DIR/$BACKUP_TO_RESTORE $THEME_DIR

# Verify rollback
if [ -f "$THEME_DIR/style.css" ]; then
    echo ""
    echo -e "${GREEN}✓ Rollback completed successfully!${NC}"
    echo ""
    echo "Restored from: $BACKUP_TO_RESTORE"
    echo "Emergency backup saved at: $EMERGENCY_BACKUP"
    echo ""
    echo "Files in theme directory:"
    ls -lh $THEME_DIR
else
    echo ""
    echo -e "${RED}✗ Rollback verification failed: style.css not found${NC}"
    echo "Attempting to restore from emergency backup..."
    rm -rf $THEME_DIR
    cp -r $EMERGENCY_BACKUP $THEME_DIR
    echo -e "${YELLOW}Emergency backup restored${NC}"
    exit 1
fi
