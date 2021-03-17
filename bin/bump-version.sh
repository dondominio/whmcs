#!/bin/bash

usage() { echo "Usage: $0 [-v <new version>]" 1>&2; exit 1; }

# get version from args

while getopts ":v:" o; do
    case "${o}" in
        v)
            version=${OPTARG}
            ;;

        *)
            usage
            ;;
    esac
done
shift $((OPTIND-1))

if [ -z "${version}" ]; then
    usage
fi

# CURRENT DIR
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# DIRS
composer=$DIR"/../composer.json"
readme=$DIR"/../README.md"
readme_en=$DIR"/../README-en.md"
addon_version=$DIR"/../src/modules/addons/dondominio/version.json"
registrar_version=$DIR"/../src/modules/registrars/dondominio/version.json"

echo "Changing version..."

last_version=$(php -r "\$obj = json_decode(utf8_encode(file_get_contents('$addon_version')));print \$obj->version;")

sed -i "s/$last_version/$version/" $composer $readme $readme_en $addon_version $registrar_version

echo "Done"

echo "Changing release date..."

last_date=$(php -r "\$obj = json_decode(utf8_encode(file_get_contents('$addon_version')));print \$obj->releaseDate;")
today=$(date +"%Y-%m-%d")

sed -i "s/$last_date/$today/" $addon_version $registrar_version

echo "Done"

echo ""
echo "Dont forget to update CHANGELOG!!"
