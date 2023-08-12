MODULE_DIRS := $(wildcard modules/* )
.PHONY: all \
		build \
		install \
		install-php \
		i18n \
		i18n-makepot \
		qa \
		scan \
		test \
		test-php \
		$(MODULE_DIRS)

include .env

all: build

build: install
	$(MAKE) build-modules
	$(MAKE) i18n
	wait

build-modules: $(MODULE_DIRS)

$(MODULE_DIRS):
	@if [ -f "$@/Makefile" ]; then echo "$@/Makefile exists!"; $(MAKE) -C $@ build; fi

install:
	$(MAKE) install-php

install-php: composer.lock
	composer install

i18n: i18n-makepot i18n-makemo

i18n-makepot:
	wp i18n make-pot . $(LANGS_PATH)/strings.pot --allow-root

i18n-makemo:
	wp i18n make-mo $(LANGS_PATH) --allow-root

qa:
	$(MAKE) test
	$(MAKE) scan
	wait

test:
	$(MAKE) test-php

test-php:
	vendor/bin/phpunit

scan:
	vendor/bin/psalm
	vendor/bin/phpcs -s
