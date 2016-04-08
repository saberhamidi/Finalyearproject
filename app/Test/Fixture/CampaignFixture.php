<?php
/**
 * Campaign Fixture
 */
class CampaignFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'text', 'null' => false),
		'type' => array('type' => 'text', 'null' => false),
		'start_date' => array('type' => 'text', 'null' => false),
		'expire_date' => array('type' => 'text', 'null' => false),
		'deployed' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '52',
			'name' => 'Christmas Campaign',
			'type' => 'Recommend for Reward',
			'start_date' => '2016-12-01',
			'expire_date' => '2016-12-25',
			'deployed' => '1'
		),
		array(
			'id' => '57',
			'name' => 'Easter Campaign',
			'type' => 'Recommend for Reward',
			'start_date' => '2016-03-20',
			'expire_date' => '2016-03-31',
			'deployed' => '1'
		),
	);

}
