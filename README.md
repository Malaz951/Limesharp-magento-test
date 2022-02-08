# Products Stock Data Updater

A Magento 2 module to update products stock data provided in an external file, support MSI.

## How to use

### Cron job

After installing the module on your Magento website, enable it from the configuration via this path

Stores > Configuration > Limesharp > Stock Status Updater > Enable Stock Data Update Module => Yes

Make your third party service place a csv file (using | separator) with the below format inside var/import directory,
and There will be a cronjob running in the background to update the stock data inside accordingly.

| SKU_A | 10  |
|-------|-----|
| SKU_B | 12  |
| SKU_C | 0   |

The cron will run by default every 10 minutes, this can be changed from the Configuration via this path

Stores > Configuration > Limesharp > Stock Status Updater > Cron Schedule

### Command

You can run this custom command to import stock data from the file, replacing with your file name

_This need more enhancements to show more helpful data about the import process in the terminal._

> php bin/magento limesharp:update-stock-data file_name=<file_name.csv>
