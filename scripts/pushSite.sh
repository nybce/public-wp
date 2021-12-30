#!/bin/bash
read -p "This will overwrite the ${3} environment with the content of ${2}, Are you sure this is correct? " -n 1 -r
echo    # (optional) move to a new line
if [[ $REPLY =~ ^[Yy]$ ]]
then

echo "Pushing Database"
./scripts/pushDb.sh ${1} ${2} ${3}
echo "Pushing Media"
./scripts/pushMedia.sh ${3}

fi
