MODULE_DIRS := $(wildcard modules/* )
SRC_DIR := .
BUILD_DIR := $(SRC_DIR)/build
DIST_DIR := $(BUILD_DIR)/dist
RELEASE_DIR := $(BUILD_DIR)/release
BUILD_ENV := 'dev'
RELEASE_WORKDIR := true
RELEASE_VERSION := dev

# List all files in the source directory, including dot files
FILES := $(wildcard $(SRC_DIR)/*) $(wildcard $(SRC_DIR)/.[!.]*)
# Exclude the special entries . and .. from the list of files
FILES := $(filter-out $(SRC_DIR)/. $(SRC_DIR)/.., $(FILES))

# List files to exclude, including DIST_DIR
EXCLUDED_FILES := ./wordpress
EXCLUDED_FILES += $(BUILD_DIR)

# Exclude files listed in EXCLUDED_FILES from FILES
FILES := $(filter-out $(EXCLUDED_FILES), $(FILES))

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

clean:
	rm -rf $(DIST_DIR)

dist: copy_dist
	$(MAKE) -C $(DIST_DIR) build BUILD_ENV=${BUILD_ENV}

release: BUILD_ENV := prod
release: dist
	@mkdir -p $(RELEASE_DIR)
	rm -r $(DIST_DIR)/.gitignore
	git -C $(DIST_DIR) config user.email "me@my.com"; \
	git -C $(DIST_DIR) config user.name "Automation"; \
	RELEASE_VERSION=$(shell echo $(RELEASE_VERSION)); \
	$$(git -C $(DIST_DIR) add --all); \
	RELEASE_REF=$$(git -C $(DIST_DIR) stash create); \
	RELEASE_REF=$$(echo "$${RELEASE_REF}" | cut -c 1-12); \
	if [ -z "$${RELEASE_REF}" ]; then \
            RELEASE_REF=$$(git -C $(DIST_DIR) rev-parse HEAD); \
            RELEASE_REF=$${RELEASE_REF:0:12}; \
        fi; \
	TIMESTAMP=$$(date -u +"%Y-%m-%d-%H-%M-%S"); \
	if [ -n "$${RELEASE_VERSION}" ]; then \
		RELEASE_META=$${RELEASE_VERSION}+$${TIMESTAMP}_$${RELEASE_REF}; \
	else \
		RELEASE_META=$${TIMESTAMP}_$${RELEASE_REF}; \
	fi; \
	ARCHIVE_FILENAME=$(PLUGIN_NAME)-$${RELEASE_META}.zip; \
	git -C $(DIST_DIR) archive --format=zip --prefix=$(PLUGIN_NAME)/ $$RELEASE_REF >$(RELEASE_DIR)/$${ARCHIVE_FILENAME}

copy_dist: clean
	@mkdir -p $(DIST_DIR)
	cp -r $(FILES) $(DIST_DIR)

build-modules: $(MODULE_DIRS)

$(MODULE_DIRS):
	@if [ -f "$@/Makefile" ]; then echo "$@/Makefile exists!"; $(MAKE) -C $@ build BUILD_ENV=${BUILD_ENV}; fi

install:
	$(MAKE) install-php

install-php: composer.lock
	if [ "$(BUILD_ENV)" = "prod" ]; then \
		composer install --no-dev --optimize-autoloader; \
	else \
		composer install; \
	fi


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
