#!/bin/bash

# CURRENT DIR
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# WHMCS DEVELOP DIR
WHMCS_DIR=""

echo "Deploying SDK..."
rm -Rf $WHMCS_DIR"/includes/dondominio"
cp -R $DIR"/../src/includes/dondominio" $WHMCS_DIR"/includes/dondominio"
echo "Done"

echo "Deploying Addon module..."
rm -Rf $WHMCS_DIR"/modules/addons/dondominio"
cp -R $DIR"/../src/modules/addons/dondominio" $WHMCS_DIR"/modules/addons/dondominio"
echo "Done"

echo "Deploying Registrar module..."
rm -Rf $WHMCS_DIR"/modules/registrars/dondominio"
cp -R $DIR"/../src/modules/registrars/dondominio" $WHMCS_DIR"/modules/registrars/dondominio"
echo "Done"

echo "Setting development settings"

# Changing endpoint URL for development
search=""
replace=""
filename=$WHMCS_DIR"/includes/dondominio/sdk/src/API.php"
sed -i "s/$search/$replace/" $filename

echo "Done"
