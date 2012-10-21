all: lady doc

lady:
	lady lady.lady > lady.php-new
	mv lady.php lady.php~`date +%s`
	mv lady.php-new lady.php
	git add lady.php

doc:
	apigen -s lady.php -d doc/
	git add doc/

.PHONY: all lady doc
