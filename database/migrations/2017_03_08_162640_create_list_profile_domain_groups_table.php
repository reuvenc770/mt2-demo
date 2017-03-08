<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListProfileDomainGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('list_profile')->create('list_profile_domain_groups', function(Blueprint $table)
		{
			$table->integer('list_profile_id')->default(0);
			$table->integer('domain_group_id')->default(0);
			$table->index(['list_profile_id','domain_group_id'], 'list_domain');
			$table->index(['domain_group_id','list_profile_id'], 'domain_list');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('list_profile')->drop('list_profile_domain_groups');
	}

}
