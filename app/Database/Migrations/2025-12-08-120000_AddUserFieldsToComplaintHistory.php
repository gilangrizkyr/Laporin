<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserFieldsToComplaintHistory extends Migration
{
    public function up()
    {
        $fields = [
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'user_id',
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'user_name',
            ],
        ];

        $this->forge->addColumn('complaint_history', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('complaint_history', ['user_name', 'user_email']);
    }
}
