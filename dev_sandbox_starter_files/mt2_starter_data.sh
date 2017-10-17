mysqldump -h 172.31.27.26 -u mt_report_ro -p --skip-lock-tables --where=" 1 limit 100 " --ignore-table=mt2_reports.cpm_reporting_actions --databases mt2_data mt2_reports attribution dima_data list_profile list_profile_export_tables mt2_shuttle mt2_temp_tables suppression > mt2_starter_data.sql
echo "use mt2_data;" >> mt2_starter_data.sql
mysqldump -h 172.31.27.26 -u mt_report_ro -p --skip-lock-tables mt2_data permissions page_permissions >> mt2_starter_data.sql
echo 'update esp_accounts set key_1="",key_2="";' >> mt2_starter_data.sql
