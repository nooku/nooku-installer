#!/bin/bash

## This script will commit whatever work you got going, force the target
## application to be reset to HEAD and will run composer install.
## This is very crude and heavy-handed and ONLY meant for testing!
##
## Use the accompanied composer.json-example file in your target Nooku setup.

# Configuration options:
NOOKU_FRAMEWORK_PATH=/Users/kotuha/Sites/misc/test/nooku-temp
DATABASE_NAME=nookucomposer

# Start script:
cd ../
REPOSITORY=`pwd`

# Commit all changes if there are any changes
CHANGED=false
if ! git diff-index --quiet HEAD --; then
    CHANGED=true
fi

if $CHANGED ; then
    echo "Creating temporary commit in $REPOSITORY"
	git commit -a -m "Temporary commit"
fi

cd "$NOOKU_FRAMEWORK_PATH"

# Initialise the nooku framework repo
if [[ ! -d "$NOOKU_FRAMEWORK_PATH/.git" ]] ; then
    git init
    git add -A
    git commit -m "Initial commit"

    mysqldump -uroot -proot "$DATABASE_NAME" > "$DATABASE_NAME.sql"
fi

# Clean-up the Nooku folder
git clean -x -f -d
git reset --hard HEAD

if [[ -d "$NOOKU_FRAMEWORK_PATH/vendor/nooku/" ]] ; then
    rm -rf "$NOOKU_FRAMEWORK_PATH/vendor/nooku"
fi

# Re-install the database
if [[ -f "$DATABASE_NAME.sql" ]] ; then
    mysqladmin -uroot -proot DROP "$DATABASE_NAME" --force
    mysqladmin -uroot -proot --default-character-set=utf8 CREATE "$DATABASE_NAME"
    mysql -uroot -proot "$DATABASE_NAME" < "$DATABASE_NAME.sql"
fi

# Show no mercy on the composer cache either
rm -rf ~/.composer/cache/

# Run composer
composer install --dev --verbose

# Reset !
cd "$REPOSITORY"

if $CHANGED ; then
	git reset --soft HEAD^
fi
