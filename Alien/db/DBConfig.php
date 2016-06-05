<?php

namespace Alien;

interface BaseDBConfig {

    const CONFIG = 'config';
    const USERS = 'users';
    const GROUPS = 'groups';
    const AUTHORIZATION = 'authorization';
    const GROUP_MEMBERS = 'group_members';
    const USER_PERMISSIONS = 'user_permissions';
    const GROUP_PERMISSIONS = 'group_permissions';
    const MESSAGES = 'messages';

}

interface ContentDBExtension {

    const WIDGETS = 'content_widgets';
    const ITEMS = 'content_items';
    const PAGES = 'content_pages';
    const TEMPLATES = 'content_templates';
    const BLOCKS = 'content_template_blocks';
    const FOLDERS = 'content_folders';
    const CONTAINERS = 'content_containers';

}

interface SchoolDBExtension {

    const COURSES = 'courses';
    const BUILDINGS = 'buildings';
    const ROOMS = 'rooms';
    const SCHEDULE = 'schedule';

}

final class DBConfig implements BaseDBConfig, ContentDBExtension, SchoolDBExtension {

    private static $prefix = '';

    private function __construct() {

    }

    private function __clone() {

    }

    public static function setDBPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function table($name) {
        $name = strtoupper($name);
        $ret = self::$prefix . '_';
        $reflection = new \ReflectionClass(__CLASS__);
        if ($reflection->hasConstant($name)) {
            return strtolower($ret . $reflection->getConstant($name));
        } else {
            return strtolower($ret . $name);
        }
    }

}