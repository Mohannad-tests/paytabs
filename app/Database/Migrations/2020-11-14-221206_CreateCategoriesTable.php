<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'         => [
				'type'           => 'INT',
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'parent_id'  => [
				'type'     => 'INT',
				'unsigned' => true,
			],
			'session_id' => [
				'type'       => 'VARCHAR',
				'constraint' => '32',
			],
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => '254',
				'null'       => false,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('categories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('categories');
	}
}
