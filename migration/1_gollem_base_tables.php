<?php

/**
 * Gollem base tables.
 *
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/gpl GPL
 * @package  Gollem
 */
class GollemBaseTables extends Horde_Db_Migration_Base
{
    /**
     * Upgrade.
     */
    public function up()
    {
        $t = $this->createTable('gollem_shares', ['autoincrementKey' => false]);
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('share_name', 'string', ['limit' => 255, 'null' => false]);
        $t->column('share_owner', 'string', ['limit' => 255, 'null' => false]);
        $t->column('share_flags', 'integer', ['default' => 0, 'null' => false]);
        $t->column('share_parents', 'text');
        $t->column('perm_creator', 'integer', ['default' => 0, 'null' => false]);
        $t->column('perm_default', 'integer', ['default' => 0, 'null' => false]);
        $t->column('perm_guest', 'integer', ['default' => 0, 'null' => false]);
        $t->column('attribute_name', 'string', ['limit' => 255, 'null' => false]);
        $t->primaryKey(['share_id']);
        $t->end();
        $this->addIndex('gollem_shares', ['share_name']);
        $this->addIndex('gollem_shares', ['share_owner']);
        $this->addIndex('gollem_shares', ['perm_creator']);
        $this->addIndex('gollem_shares', ['perm_default']);
        $this->addIndex('gollem_shares', ['perm_guest']);

        $t = $this->createTable('gollem_shares_groups');
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('group_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm', 'integer', ['null' => false]);
        $t->end();

        $this->addIndex('gollem_shares_groups', ['share_id']);
        $this->addIndex('gollem_shares_groups', ['group_uid']);
        $this->addIndex('gollem_shares_groups', ['perm']);

        $t = $this->createTable('gollem_shares_users');
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('user_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm', 'integer', ['null' => false]);
        $t->end();

        $this->addIndex('gollem_shares_users', ['share_id']);
        $this->addIndex('gollem_shares_users', ['user_uid']);
        $this->addIndex('gollem_shares_users', ['perm']);

        $t = $this->createTable('gollem_sharesng', ['autoincrementKey' => 'share_id']);
        $t->column('share_name', 'string', ['limit' => 255, 'null' => false]);
        $t->column('share_owner', 'string', ['limit' => 255]);
        $t->column('share_flags', 'integer', ['default' => 0, 'null' => false]);
        $t->column('share_parents', 'text');
        $t->column('perm_creator_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('attribute_name', 'string', ['limit' => 255, 'null' => false]);
        $t->end();

        $this->addIndex('gollem_sharesng', ['share_name']);
        $this->addIndex('gollem_sharesng', ['share_owner']);
        $this->addIndex('gollem_sharesng', ['perm_creator_' . Horde_Perms::SHOW]);
        $this->addIndex('gollem_sharesng', ['perm_creator_' . Horde_Perms::READ]);
        $this->addIndex('gollem_sharesng', ['perm_creator_' . Horde_Perms::EDIT]);
        $this->addIndex('gollem_sharesng', ['perm_creator_' . Horde_Perms::DELETE]);
        $this->addIndex('gollem_sharesng', ['perm_default_' . Horde_Perms::SHOW]);
        $this->addIndex('gollem_sharesng', ['perm_default_' . Horde_Perms::READ]);
        $this->addIndex('gollem_sharesng', ['perm_default_' . Horde_Perms::EDIT]);
        $this->addIndex('gollem_sharesng', ['perm_default_' . Horde_Perms::DELETE]);
        $this->addIndex('gollem_sharesng', ['perm_guest_' . Horde_Perms::SHOW]);
        $this->addIndex('gollem_sharesng', ['perm_guest_' . Horde_Perms::READ]);
        $this->addIndex('gollem_sharesng', ['perm_guest_' . Horde_Perms::EDIT]);
        $this->addIndex('gollem_sharesng', ['perm_guest_' . Horde_Perms::DELETE]);

        $t = $this->createTable('gollem_sharesng_groups', ['autoincrementKey' => false]);
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('group_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->end();

        $this->addIndex('gollem_sharesng_groups', ['share_id']);
        $this->addIndex('gollem_sharesng_groups', ['group_uid']);
        $this->addIndex('gollem_sharesng_groups', ['perm_' . Horde_Perms::SHOW]);
        $this->addIndex('gollem_sharesng_groups', ['perm_' . Horde_Perms::READ]);
        $this->addIndex('gollem_sharesng_groups', ['perm_' . Horde_Perms::EDIT]);
        $this->addIndex('gollem_sharesng_groups', ['perm_' . Horde_Perms::DELETE]);

        $t = $this->createTable('gollem_sharesng_users', ['autoincrementKey' => false]);
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('user_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->end();

        $this->addIndex('gollem_sharesng_users', ['share_id']);
        $this->addIndex('gollem_sharesng_users', ['user_uid']);
        $this->addIndex('gollem_sharesng_users', ['perm_' . Horde_Perms::SHOW]);
        $this->addIndex('gollem_sharesng_users', ['perm_' . Horde_Perms::READ]);
        $this->addIndex('gollem_sharesng_users', ['perm_' . Horde_Perms::EDIT]);
        $this->addIndex('gollem_sharesng_users', ['perm_' . Horde_Perms::DELETE]);
    }

    /**
     * Downgrade
     */
    public function down()
    {
        $this->dropTable('gollem_shares');
        $this->dropTable('gollem_shares_users');
        $this->dropTable('gollem_shares_groups');
        $this->dropTable('gollem_sharesng');
        $this->dropTable('gollem_sharesng_groups');
        $this->dropTable('gollem_sharesng_users');
    }
}
