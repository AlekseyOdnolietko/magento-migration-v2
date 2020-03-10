## Migration of custom tables
### Migration Steps:
1. Install Magento v1.9.4.4 with sample data (ver 1.9.2.4 and later).
2. Install empty Magento v2.3.4 via composer.
3. Copy images from M1 media folder to M2 pub/media folder.
4. Store media files in the Magento database:
    M1: System > Configuration > ADVANCED > System > Storage Configuration
   select Media Database, select Media Storage and synchronize.
   Repeat the same n Magento 2 Admin panel.
4. Run the following command from M2 root dir:
```bash
    composer require funch88/magento-migration
```
5. Upgrade Magento:
```bash
   php bin/magento setup:upgrade
```
6. Update config.xml according to the credentials:
```xml
    <!-- ... -->
    <source>
        <database host="" name="" user="" password="" port=""/><!-- mandatory -->
    </source>
    <destination>
        <database host="" name="" user="" password="" port=""/><!-- mandatory -->
    </destination>
    <!-- ... -->
    <options>
        <!-- ... -->
        <source_prefix></source_prefix><!-- optional -->
        <dest_prefix></dest_prefix><!-- optional -->
        <!-- ... -->
        <crypt_key></crypt_key><!-- mandatory -->
    </options>
```
7. Import the sql dump and run migration_generate_data function
8. Migrate settings and data
```bash
    php bin/magento migrate:settings vendor/funch88/magento-migration/etc/opensource-to-opensource/1.9.4.4/config.xml
    php bin/magento migrate:data vendor/funch88/magento-migration/etc/opensource-to-opensource/1.9.4.4/config.xml
```
9. Flush all Magento 2 cache types
10. Reindex all Magento 2 indexes



