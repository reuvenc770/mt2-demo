<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrontoReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('reporting_data')->create('bronto_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('esp_account_id')->unsigned();
			$table->string('message_name', 100);
			$table->string('internal_id', 100);
			$table->dateTime('start');
			$table->string('message_id', 100);
			$table->string('status', 100);
			$table->string('type', 100);
			$table->string('from_email', 100);
			$table->string('from_name', 100);
			$table->boolean('authentication');
			$table->boolean('reply_tracking');
			$table->integer('throttle');
			$table->string('fatigue_override', 100);
			$table->integer('num_sends')->unsigned()->default(0);
			$table->integer('num_deliveries')->unsigned()->default(0);
			$table->integer('num_hard_bad_email')->unsigned()->default(0);
			$table->integer('num_hard_dest_unreach')->unsigned()->default(0);
			$table->integer('num_hard_message_content')->unsigned()->default(0);
			$table->integer('num_hard_bounces')->unsigned()->default(0);
			$table->integer('num_soft_bad_email')->unsigned()->default(0);
			$table->integer('num_soft_dest_unreach')->unsigned()->default(0);
			$table->integer('num_soft_message_content')->unsigned()->default(0);
			$table->integer('num_other_bounces')->unsigned()->default(0);
			$table->integer('num_soft_bounces')->unsigned()->default(0);
			$table->integer('num_bounces')->unsigned()->default(0);
			$table->integer('uniq_opens')->unsigned()->default(0);
			$table->integer('num_opens')->unsigned()->default(0);
			$table->float('avg_opens')->unsigned()->default(0.00);
			$table->integer('uniq_clicks')->unsigned()->default(0);
			$table->integer('num_clicks')->unsigned()->default(0);
			$table->float('avg_clicks')->unsigned()->default(0.00);
			$table->integer('uniq_conversions')->unsigned()->default(0);
			$table->integer('num_conversions')->unsigned()->default(0);
			$table->float('avg_conversions')->unsigned()->default(0.00);
			$table->integer('revenue')->unsigned()->default(0);
			$table->integer('num_survey_responses')->unsigned()->default(0);
			$table->integer('num_friend_forwards')->unsigned()->default(0);
			$table->integer('num_contact_updates')->unsigned()->default(0);
			$table->integer('num_unsubscribes_by_prefs')->unsigned()->default(0);
			$table->integer('num_unsubscribes_by_complaint')->unsigned()->default(0);
			$table->integer('num_contact_loss')->unsigned()->default(0);
			$table->integer('num_contact_loss_bounces')->unsigned()->default(0);
			$table->float('delivery_rate')->unsigned()->default(0.00);
			$table->float('open_rate')->unsigned()->default(0.00);
			$table->float('click_rate')->unsigned()->default(0.00);
			$table->float('click_through_rate')->unsigned()->default(0.00);
			$table->float('conversion_rate')->unsigned()->default(0.00);
			$table->float('bounce_rate')->unsigned()->default(0.00);
			$table->float('complaint_rate')->unsigned()->default(0.00);
			$table->float('contact_loss_rate')->unsigned()->default(0.00);
			$table->integer('num_social_shares')->unsigned()->default(0);
			$table->integer('num_shares_facebook')->unsigned()->default(0);
			$table->integer('num_shares_twitter')->unsigned()->default(0);
			$table->integer('num_shares_linked_in')->unsigned()->default(0);
			$table->integer('num_shares_digg')->unsigned()->default(0);
			$table->integer('num_shares_my_space')->unsigned()->default(0);
			$table->integer('num_views_facebook')->unsigned()->default(0);
			$table->integer('num_views_twitter')->unsigned()->default(0);
			$table->integer('num_views_linked_in')->unsigned()->default(0);
			$table->integer('num_views_digg')->unsigned()->default(0);
			$table->integer('num_views_my_space')->unsigned()->default(0);
			$table->integer('num_social_views')->unsigned()->default(0);
			$table->string('reply_email')->default('');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('reporting_data')->drop('bronto_reports');
	}

}
