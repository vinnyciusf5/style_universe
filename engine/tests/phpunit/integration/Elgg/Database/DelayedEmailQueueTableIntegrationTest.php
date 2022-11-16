<?php

namespace Elgg\Database;

use Elgg\Email\DelayedQueue\DatabaseRecord;
use Elgg\IntegrationTestCase;
use Elgg\Notifications\Notification;

class DelayedEmailQueueTableIntegrationTest extends IntegrationTestCase {

	/**
	 * @var DelayedEmailQueueTable
	 */
	protected $table;

	/**
	 * {@inheritDoc}
	 */
	public function up() {
		$this->table = _elgg_services()->delayedEmailQueueTable;
	}
	
	/**
	 * Create test notification
	 *
	 * @return Notification
	 */
	protected function getTestNotification(): Notification {
		$recipient = $this->createUser();
		$sender = $this->createUser();
		
		return new Notification($sender, $recipient, 'en', 'Test subject', 'Test body');
	}
	
	public function testqueueEmail() {
		$notification = $this->getTestNotification();
		$recipient = $notification->getRecipient();
		
		// insert
		$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', $notification));
		
		$dt = $this->table->getCurrentTime('-10 seconds');
		
		// retrieve before inserted time
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
		
		$dt = $this->table->getCurrentTime('+10 seconds');
		
		// retrieve after inserted time
		$rows = $this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp());
		$this->assertCount(1, $rows);
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'weekly', $dt->getTimestamp()));
		
		$row = $rows[0];
		$this->assertInstanceOf(DatabaseRecord::class, $row);
		
		$this->assertEquals($row, $this->table->getRow($row->id));
		
		// validate row contents
		$this->assertEquals($recipient->guid, $row->recipient_guid);
		$this->assertEquals('daily', $row->delivery_interval);
		$this->assertEquals(unserialize(serialize($notification)), $row->getNotification());
		
		// delete
		$this->assertTrue($row->delete());
		
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
	}
	
	public function testGetIntervalRows() {
		
		// add testing rows
		for ($i = 0; $i < 5; $i++) {
			$notification = $this->getTestNotification();
			$recipient = $notification->getRecipient();
			
			// insert
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', $notification));
		}
		
		// different interval
		for ($i = 0; $i < 5; $i++) {
			$notification = $this->getTestNotification();
			$recipient = $notification->getRecipient();
			
			// insert
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'weekly', $notification));
		}
		
		$dt = $this->table->getCurrentTime('+10 seconds');
		
		// retrieve
		$this->assertCount(5, $this->table->getIntervalRows('daily', $dt->getTimestamp()));
		$this->assertCount(5, $this->table->getIntervalRows('weekly', $dt->getTimestamp()));
	}
	
	public function testDeleteRecipientRows() {
		$notification = $this->getTestNotification();
		$recipient = $notification->getRecipient();
		
		// insert
		for ($i = 0; $i < 5; $i++) {
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', $notification));
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'weekly', $notification));
		}
		
		$dt = $this->table->getCurrentTime('+10 seconds');
		
		// delete
		$this->assertEquals(5, $this->table->deleteRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
		$this->assertEquals(5, $this->table->deleteRecipientRows($recipient->guid, 'weekly', $dt->getTimestamp()));
		
		// verify
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'weekly', $dt->getTimestamp()));
	}
	
	public function testDeleteAllRecipientRows() {
		$notification = $this->getTestNotification();
		$recipient = $notification->getRecipient();
		
		// insert
		for ($i = 0; $i < 5; $i++) {
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', $notification));
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'weekly', $notification));
		}
		
		$dt = $this->table->getCurrentTime('+10 seconds');
		
		// delete
		$this->assertEquals(10, $this->table->deleteAllRecipientRows($recipient->guid));
		
		// verify
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'weekly', $dt->getTimestamp()));
	}
	
	public function testRecipientRowsDeletedOnRecipientDelete() {
		$recipient = $this->createUser();
		$recipient_guid = $recipient->guid;
		
		// insert
		for ($i = 0; $i < 5; $i++) {
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', []));
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'weekly', []));
		}
				
		// delete
		$this->assertTrue(elgg_call(ELGG_IGNORE_ACCESS, function() use ($recipient) {
			return $recipient->delete();
		}));
		
		// verify
		$dt = $this->table->getCurrentTime('+10 seconds');
		$this->assertEmpty($this->table->getRecipientRows($recipient_guid, 'daily', $dt->getTimestamp()));
		$this->assertEmpty($this->table->getRecipientRows($recipient_guid, 'weekly', $dt->getTimestamp()));
	}
	
	public function testUpdateRecipientInterval() {
		$notification = $this->getTestNotification();
		$recipient = $notification->getRecipient();
		
		// insert
		for ($i = 0; $i < 5; $i++) {
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'daily', $notification));
		}
		
		// different interval
		for ($i = 0; $i < 5; $i++) {
			$this->assertTrue($this->table->queueEmail($recipient->guid, 'weekly', $notification));
		}
		
		$dt = $this->table->getCurrentTime('+10 seconds');
		
		// update
		$this->assertTrue($this->table->updateRecipientInterval($recipient->guid, 'weekly'));
		
		// verify
		$this->assertEmpty($this->table->getRecipientRows($recipient->guid, 'daily', $dt->getTimestamp()));
		$this->assertCount(10, $this->table->getRecipientRows($recipient->guid, 'weekly', $dt->getTimestamp()));
	}
}
