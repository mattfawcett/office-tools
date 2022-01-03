build_docker_image:
	docker build -t webmerge-office-tools-test-harness .
test:
	USE_API_CACHE=0 vendor/bin/phpunit tests/
test_cache:
	USE_API_CACHE=1 vendor/bin/phpunit tests/
test_in_docker:
	docker run --rm -v "${PWD}:/app" -e USE_API_CACHE=0 webmerge-office-tools-test-harness:latest "tests/"
test_in_docker_cache:
	docker run --rm -v "${PWD}:/app" -e USE_API_CACHE=1 webmerge-office-tools-test-harness:latest "tests/"
