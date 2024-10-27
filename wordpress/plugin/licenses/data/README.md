# Open Source Licenses Data

This directory will contain the data for the Open Source Licenses plugin.

It is copied into the plugin directory during the build process (i.e., Github Actions).

## licenses.json

This is the enhanced data from the original data.

It is a JSON file containing an array of license objects.

It was created using the `enhance.py` script.

It will be populated into the database using the `License_Populator` class.
