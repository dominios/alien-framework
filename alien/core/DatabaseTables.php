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

}

interface ContentDBExtension {

    const WIDGETS = 'content_views';
    const ITEMS = 'content_items';
    const PAGES = 'content_pages';
    const TEMPLATES = 'content_templates';
    const FOLDERS = 'content_folders';
    const ITEM_TYPES = 'content_item_types';
    const CONTAINERS = 'content_containers';

}

final class DBConfig implements BaseDBConfig, ContentDBExtension {

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
            return $ret . $reflection->getConstant($name);
        } else {
            return $ret . $name;
        }
    }

}

?>
