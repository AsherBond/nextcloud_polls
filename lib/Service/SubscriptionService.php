<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 *
 * @author René Gieling <github@dartcafe.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Polls\Service;

use OCA\Polls\Db\Subscription;
use OCA\Polls\Db\SubscriptionMapper;
use OCA\Polls\Model\Acl;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\DB\Exception;

class SubscriptionService {
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function __construct(
		private SubscriptionMapper $subscriptionMapper,
		private Acl $acl,
	) {
	}

	public function get(?int $pollId = null): bool {
		$this->acl->setPollId($pollId, Acl::PERMISSION_POLL_SUBSCRIBE);
		try {
			$this->subscriptionMapper->findByPollAndUser($this->acl->getPollId(), $this->acl->getUserId());
			// Subscription exists
			return true;
		} catch (DoesNotExistException $e) {
			return false;
		}
	}

	public function set(bool $subscribed, ?int $pollId = null): bool {
		$this->acl->setPollId($pollId, Acl::PERMISSION_POLL_SUBSCRIBE);
		if (!$subscribed) {
			try {
				$subscription = $this->subscriptionMapper->findByPollAndUser($this->acl->getPollId(), $this->acl->getUserId());
				$this->subscriptionMapper->delete($subscription);
			} catch (DoesNotExistException $e) {
				// catch silently (assume already unsubscribed)
			}
		} else {
			try {
				$this->add($this->acl->getPollId(), $this->acl->getUserId());
			} catch (Exception $e) {
				if ($e->getReason() === Exception::REASON_UNIQUE_CONSTRAINT_VIOLATION) {
					// catch silently (assume already subscribed)
				} else {
					throw $e;
				}
			}
		}
		return $subscribed;
	}

	private function add(int $pollId, string $userId): void {
		$subscription = new Subscription();
		$subscription->setPollId($pollId);
		$subscription->setUserId($userId);
		$this->subscriptionMapper->insert($subscription);
	}
}
