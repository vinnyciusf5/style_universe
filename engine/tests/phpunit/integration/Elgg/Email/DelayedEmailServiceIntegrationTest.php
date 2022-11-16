<?php

namespace Elgg\Email;

use Elgg\IntegrationTestCase;
use Elgg\Notifications\Notification;
use Elgg\Notifications\NotificationEventHandler;
use Elgg\Notifications\SubscriptionNotificationEvent;
use Laminas\Mail\Message;

class DelayedEmailServiceIntegrationTest extends IntegrationTestCase {

	/**
	 * @var DelayedEmailService
	 */
	protected $service;
	
	/**
	 * {@inheritDoc}
	 */
	public function up() {
		$this->createApplication([
			'isolate' => true,
		]);
		
		$this->service = _elgg_services()->delayedEmailService;
	}
	
	/**
	 * Create test notification
	 *
	 * @return Notification
	 */
	protected function getTestNotification(): Notification {
		$recipient = $this->createUser();
		$sender = $this->createUser();
		$object = $this->createObject([
			'owner_guid' => $sender->guid,
		]);
		
		$params = [
			'event' => new SubscriptionNotificationEvent($object, 'create', $sender),
			'object' => $object,
			'action' => 'create',
			'method' => 'delayed_email',
			'recipient' => $recipient,
			'sender' => $sender,
		];
		
		$params['handler'] = new NotificationEventHandler($params['event'], _elgg_services()->notifications, $params);
		
		return new Notification($sender, $recipient, 'en', 'Test subject', 'Test body', 'Test summary', $params);
	}
	
	public function testEnqueueNotification() {
		$notification = $this->getTestNotification();
		
		$this->assertTrue($this->service->enqueueNotification($notification));
	}
	
	public function testProcessQueuedNotifications() {
		// queue a few notifications
		for ($i = 0; $i < 5; $i++) {
			$notification = $this->getTestNotification();
			
			$this->assertTrue($this->service->enqueueNotification($notification));
		}
		
		// proccess queue
		$this->service->processQueuedNotifications('daily', time() + 10);
		
		/* @var $mailer \Laminas\Mail\Transport\InMemory */
		$mailer = _elgg_services()->mailer;
	
		$message = $mailer->getLastMessage();
		$this->assertInstanceOf(Message::class, $message);
		
		$this->assertEquals(elgg_echo('notifications:delayed_email:subject:daily'), $message->getSubject());
	}
}
