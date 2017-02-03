Filestack Plugin
=============================

Developer info: [Pimcore at basilicom](http://basilicom.de/en/pimcore)

Please note: This project uses Filestack https://www.Filestack.com/

## Synopsis

This plugin adds a new button [F] 'Upload via Filestack' in the asset *folder* view tab.
By clicking this button the Filestack file selector window opens and you
can choose to upload one or multiple (limited to 100) assets via a lot of services.

You need to aquire a Filestack API key at https://dev.filestack.com/register/free

## Installation

Add the "basilicom-pimcore-plugin/filestack" requirement to the composer.json 
in the toplevel directory of your pimcore installation. 

Example:

    {
        "require": {
            "basilicom-pimcore-plugin/filestack": ">=1.0.0"
        }
    }

Or run:

    composer require basilicom-pimcore-plugin/filestack


Then enable and install the Filestack plugin in
Pimcore Extension Manager (under Extras > Extensions).

Using the edit (pencil) icon in the Extension Manager, open the XML 
plugin configuration file and set your Filestack API key.
Afterwards, you need to reload the Pimcore backend.

## License

GNU General Public License version 3 (GPLv3)