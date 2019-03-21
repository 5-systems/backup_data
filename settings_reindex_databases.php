<?php

	// Settings
        $server_name='SERVER_NAME';

	// Directories
	$log_file="/home/Backup/datacenter_reindex_databases.log";
	
	// List of bases
	$list_of_bases=Array();
	$list_of_bases[]='database_name';

        // Mail peremeters
        $SendMailFrom='post@5-systems.ru';
        $SendMailTo=Array();
        $SendMailTo[]='account@5-systems.ru';

        $SMTPHost = 'smtp.yandex.ru';
        $SMTPUser = 'post@5-systems.ru';
        $SMTPPassword = 'passwd';
        $SMTPPort = 465;
        $SMTPSecure='ssl';
        $SMTPDebug=0;        
	// End settings

?>
