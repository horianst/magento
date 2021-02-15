Copiaza modulul in root/app/code

in linia de comanda intra in project root
vezi modulele 
bin/magento module:status

Modulul Urgent_Cargus ar trebui sa fie disabled



activeaza modulul
bin/magento module:enable Urgent_Cargus

verifica activarea
bin/magento module:status
Modulul Urgent_Cargus ar trebui sa fie enabled


Faceti un update
bin/magento setup:upgrade
bin/magento setup:di:compile

Posibil ca aceste activitati sa fi afectat accesabilitatea proiectului
Actualizeaza drepturile asupra proiectului

php bin/magento cache:flush
chmod -R 775 /<path>/<to>/<root>
chown -R nobody

[comment]: <> (chown -R apache: /<path>/<to>/<root>)

acceseaza pagina de admin

dute la stores/configuration

click pe sales/Shipping Methods/Cargus Shipping

adauga 
api url
subscription key
username
password

php bin/magento cache:flush
