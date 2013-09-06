test:
	cat example.lady | php lady.php > example.actual
	diff -u example.php example.actual > example.diff
	rm example.actual example.diff

.PHONY: test
