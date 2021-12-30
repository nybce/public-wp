#!/bin/bash
read -p "This will download the ${1} envrionment, Are you sure this is correct? " -n 1 -r
echo    # (optional) move to a new line
if [[ $REPLY =~ ^[Yy]$ ]]
then

echo "Pulling Database"
./scripts/pullDb.sh ${1}
echo "Pulling Media"
./scripts/pullMedia.sh ${1}

fi
