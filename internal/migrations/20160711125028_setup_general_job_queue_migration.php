<?php

use Phinx\Migration\AbstractMigration;

class SetupGeneralJobQueueMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('general_job_queue');
        $table->addColumn('queue_name','string',['limit' => 128])
            ->addColumn('creation_timestamp','datetime',['limit' => 6])
            ->addColumn('time_to_live','int',['limit' => 11])
            ->addColumn('last_action_timestamp','datetime',['limit' => 6])
            ->addColumn('status','string',['limit' => 20])
            ->addColumn('failure_code','int',['default' => 0, 'limit' => 3])
            ->addColumn('no_of_unsuccessful_attempts','int',['limit' => 3, 'default' => 0])
            ->addColumn('max_no_of_retries','int',['limit' => 3, 'default' => 0])
            ->addColumn('payload','text',['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM])
            ->addColumn('failure_data','text',['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM])
            ->addIndex(['queue_name','status'])
            ->create();
    }
}
